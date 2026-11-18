<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
class Index extends Component
{
    use WithPagination;

    // Búsqueda y filtros
    public string $search = '';

    public string $status = 'all';

    // Estado del formulario
    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    // Información del producto
    public string $categoryId = '';

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public string $comparePrice = '';

    public bool $isActive = true;

    public bool $isFeatured = false;

    public int $sortOrder = 0;

    // Variantes de talla y color
    public array $variants = [];

    // Tallas predefinidas para selección rápida
    public array $commonSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', 'ÚNICA'];

    // Entrada temporal para agregar una nueva talla personalizada por variante
    public array $newSizeInputs = [];

    // Mensajes
    public string $notice = '';

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    // ========================================
    // CREAR PRODUCTO
    // ========================================

    public function create(): void
    {
        Gate::authorize('access-admin');

        $this->resetForm();

        $this->sortOrder = (int) Product::max('sort_order') + 1;

        $this->addVariant();

        $this->showForm = true;
    }

    // ========================================
    // EDITAR PRODUCTO
    // ========================================

    public function edit(int $id): void
    {
        Gate::authorize('access-admin');

        $product = Product::with('variants')
            ->findOrFail($id);

        $this->resetForm();

        $this->editingId = $product->id;
        $this->categoryId = (string) $product->category_id;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->price = (string) $product->price;
        $this->comparePrice = (string) ($product->compare_price ?? '');

        $this->isActive = $product->is_active;
        $this->isFeatured = $product->is_featured;
        $this->sortOrder = $product->sort_order;

        $this->variants = $product->variants
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'size' => $variant->size,
                'color_name' => $variant->color_name,
                'color_hex' => $variant->color_hex ?? '',
                'stock' => $variant->stock,
                'is_active' => $variant->is_active,
            ])
            ->values()
            ->all();

        if (empty($this->variants)) {
            $this->addVariant();
        }

        $this->showForm = true;
    }

    // ========================================
    // AGREGAR VARIANTE
    // ========================================

    public function addVariant(): void
    {
        Gate::authorize('access-admin');

        if (count($this->variants) >= 40) {
            return;
        }

        $this->variants[] = [
            'id' => null,
            'sku' => '',
            'size' => '',
            'color_name' => '',
            'color_hex' => '',
            'stock' => 0,
            'is_active' => true,
        ];
    }

    // ========================================
    // QUITAR VARIANTE DEL FORMULARIO
    // ========================================

    public function removeVariant(int $index): void
    {
        Gate::authorize('access-admin');

        unset($this->variants[$index]);
        unset($this->newSizeInputs[$index]);

        $this->variants = array_values($this->variants);
        $this->newSizeInputs = array_values($this->newSizeInputs);
    }

    // ========================================
    // SELECCIÓN Y GESTIÓN DE TALLA
    // ========================================

    public function selectVariantSize(int $index, string $size): void
    {
        if (! isset($this->variants[$index])) {
            return;
        }

        $size = trim($size);
        $currentSizes = array_filter(array_map('trim', explode(',', $this->variants[$index]['size'] ?? '')));

        if (in_array($size, $currentSizes, true)) {
            // Si ya está seleccionada, la desmarcamos
            $currentSizes = array_values(array_filter($currentSizes, fn ($s) => $s !== $size));
        } else {
            // Si no está, la agregamos a la lista
            $currentSizes[] = $size;
        }

        $this->variants[$index]['size'] = implode(', ', $currentSizes);
    }

    public function addCustomSize(int $index): void
    {
        $customSize = Str::upper(trim($this->newSizeInputs[$index] ?? ''));
        if ($customSize !== '' && isset($this->variants[$index])) {
            $currentSizes = array_filter(array_map('trim', explode(',', $this->variants[$index]['size'] ?? '')));
            if (! in_array($customSize, $currentSizes, true)) {
                $currentSizes[] = $customSize;
                $this->variants[$index]['size'] = implode(', ', $currentSizes);
            }
            if (! in_array($customSize, $this->commonSizes, true)) {
                $this->commonSizes[] = $customSize;
            }
            $this->newSizeInputs[$index] = '';
        }
    }

    // ========================================
    // GUARDAR PRODUCTO Y VARIANTES
    // ========================================

    public function save(): void
    {
        Gate::authorize('access-admin');

        $this->name = trim($this->name);

        // Normalizar valores antes de validar.
        foreach ($this->variants as $index => $variant) {
            $this->variants[$index] = [
                'id' => $variant['id'] ?? null,
                'sku' => Str::upper(trim($variant['sku'] ?? '')),
                'size' => Str::upper(trim($variant['size'] ?? '')),
                'color_name' => trim($variant['color_name'] ?? ''),
                'color_hex' => Str::upper(trim($variant['color_hex'] ?? '')),
                'stock' => $variant['stock'] ?? 0,
                'is_active' => $variant['is_active'] ?? true,
            ];
        }

        $this->variants = array_values($this->variants);

        // Verificar que los IDs de variantes
        // pertenezcan al producto en edición.
        $allowedIds = $this->editingId
            ? Product::findOrFail($this->editingId)
                ->variants()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        foreach ($this->variants as $variant) {
            $id = (int) ($variant['id'] ?? 0);

            if ($id && ! in_array($id, $allowedIds, true)) {
                abort(422, 'Variante no válida para este producto.');
            }
        }

        // Reglas principales
        $rules = [
            'categoryId' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'comparePrice' => [
                'nullable',
                'numeric',
                'gt:price',
                'max:99999999',
            ],
            'isActive' => ['boolean'],
            'isFeatured' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:9999'],

            'variants' => ['required', 'array', 'min:1', 'max:40'],
            'variants.*.size' => ['required', 'string', 'max:120'],
            'variants.*.color_name' => ['nullable', 'string', 'max:60'],
            'variants.*.color_hex' => [
                'nullable',
                'regex:/^#[0-9A-F]{6}$/',
            ],
            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
            'variants.*.is_active' => ['boolean'],
        ];

        // SKU único para cada variante.
        foreach ($this->variants as $index => $variant) {
            $skuRule = Rule::unique('product_variants', 'sku');

            if (! empty($variant['id'])) {
                $skuRule->ignore((int) $variant['id']);
            }

            $rules["variants.$index.sku"] = [
                'required',
                'string',
                'max:70',
                'distinct',
                $skuRule,
            ];
        }

        $this->validate($rules, [
            'categoryId.required' => 'Selecciona una categoría.',
            'name.required' => 'Ingresa el nombre del producto.',
            'price.required' => 'Ingresa el precio.',
            'comparePrice.gt' => 'El precio anterior debe ser mayor al actual.',
            'variants.min' => 'Agrega al menos una variante.',
            'variants.*.sku.required' => 'Ingresa el SKU.',
            'variants.*.sku.unique' => 'Este SKU ya está registrado.',
            'variants.*.sku.distinct' => 'No repitas el SKU.',
            'variants.*.size.required' => 'Selecciona una talla.',
            'variants.*.color_name.required' => 'Ingresa el color.',
            'variants.*.color_hex.regex' => 'El color debe tener formato #RRGGBB.',
            'variants.*.stock.min' => 'El stock no puede ser negativo.',
        ]);

        // No permitir repetir talla y color
        // dentro del mismo producto.
        $combinations = [];

        foreach ($this->variants as $index => $variant) {
            $key = Str::lower(
                $variant['size'].'|'.$variant['color_name']
            );

            if (isset($combinations[$key])) {
                throw ValidationException::withMessages([
                    "variants.$index.color_name" => 'Esta combinación de talla y color ya existe.',
                ]);
            }

            $combinations[$key] = true;
        }

        $isEditing = $this->editingId !== null;

        DB::transaction(function () use ($isEditing) {

            $product = $isEditing
                ? Product::findOrFail($this->editingId)
                : new Product;

            // Crear slug único únicamente al registrar.
            if (! $isEditing) {
                $base = Str::slug($this->name) ?: 'producto';
                $slug = $base;
                $number = 2;

                while (
                    Product::withTrashed()
                        ->where('slug', $slug)
                        ->exists()
                ) {
                    $slug = $base.'-'.$number++;
                }

                $product->slug = $slug;
            }

            $product->fill([
                'category_id' => (int) $this->categoryId,
                'name' => $this->name,
                'description' => trim($this->description) ?: null,
                'price' => $this->price,
                'compare_price' => $this->comparePrice !== ''
                    ? $this->comparePrice
                    : null,
                'is_active' => $this->isActive,
                'is_featured' => $this->isFeatured,
                'sort_order' => $this->sortOrder,
            ]);

            $product->save();

            $keptIds = [];

            foreach ($this->variants as $variant) {
                $variantModel = ! empty($variant['id'])
                    ? $product->variants()
                        ->findOrFail((int) $variant['id'])
                    : $product->variants()->make();

                $variantModel->fill([
                    'sku' => $variant['sku'],
                    'size' => $variant['size'],
                    'color_name' => $variant['color_name'] ?? '',
                    'color_hex' => $variant['color_hex'] ?: null,
                    'stock' => (int) $variant['stock'],
                    'is_active' => (bool) $variant['is_active'],
                ]);

                $variantModel->save();

                $keptIds[] = $variantModel->id;
            }

            // Quitar variantes eliminadas del formulario.
            $product->variants()
                ->whereNotIn('id', $keptIds)
                ->delete();
        });

        $this->notice = $isEditing
            ? 'Producto actualizado correctamente.'
            : 'Producto creado correctamente.';

        $this->cancelForm();
        $this->resetPage();
    }

    // ========================================
    // ARCHIVAR PRODUCTO
    // ========================================

    public function archive(int $id): void
    {
        Gate::authorize('access-admin');

        Product::findOrFail($id)->delete();

        $this->notice = 'Producto archivado correctamente.';
    }

    // ========================================
    // RESTAURAR PRODUCTO
    // ========================================

    public function restore(int $id): void
    {
        Gate::authorize('access-admin');

        Product::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        $this->notice = 'Producto restaurado correctamente.';
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'categoryId',
            'name',
            'description',
            'price',
            'comparePrice',
            'isActive',
            'isFeatured',
            'sortOrder',
            'variants',
        ]);

        $this->resetValidation();
    }

    // ========================================
    // LISTADO DE PRODUCTOS
    // ========================================

    public function render()
    {
        $query = Product::query()
            ->with(['category', 'images'])
            ->withSum('variants', 'stock');

        if ($this->status === 'archived') {
            $query->onlyTrashed();
        } elseif ($this->status === 'active') {
            $query->where('is_active', true);
        } elseif ($this->status === 'inactive') {
            $query->where('is_active', false);
        }

        if (trim($this->search) !== '') {
            $query->where(
                'name',
                'like',
                '%'.trim($this->search).'%'
            );
        }

        return view('livewire.admin.products.index', [
            'products' => $query
                ->orderBy('sort_order')
                ->latest('id')
                ->paginate(10),

            'categories' => Category::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
