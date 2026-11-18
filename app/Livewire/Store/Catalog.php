<?php

namespace App\Livewire\Store;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class Catalog extends Component
{
    use WithPagination;

    // Categoría de la URL
    #[Locked]
    public ?int $categoryId = null;

    // Búsqueda y ordenamiento existentes
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'orden', except: 'recientes')]
    public string $sort = 'recientes';

    // Nuevos filtros
    #[Url(as: 'talla', except: '')]
    public string $size = '';

    #[Url(as: 'color', except: '')]
    public string $color = '';

    #[Url(as: 'min', except: '')]
    public string $minPrice = '';

    #[Url(as: 'max', except: '')]
    public string $maxPrice = '';

    #[Url(as: 'disponibles', except: false)]
    public bool $onlyInStock = false;

    // ========================================
    // INICIALIZAR CATÁLOGO
    // ========================================

    public function mount(?string $categorySlug = null): void
    {
        if ($categorySlug !== null) {
            $category = Category::query()
                ->where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();

            $this->categoryId = $category->id;
        }
    }

    // ========================================
    // REINICIAR PAGINACIÓN AL FILTRAR
    // ========================================

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'sort',
            'size',
            'color',
            'minPrice',
            'maxPrice',
            'onlyInStock',
        ], true)) {
            $this->resetPage();
        }
    }

    // ========================================
    // LIMPIAR FILTROS
    // ========================================

    public function clearFilters(): void
    {
        $this->reset([
            'size',
            'color',
            'minPrice',
            'maxPrice',
            'onlyInStock',
        ]);

        $this->resetPage();
    }

    // ========================================
    // VALIDAR PRECIOS INTRODUCIDOS
    // ========================================

    private function validPrice(string $value): ?string
    {
        $value = trim($value);

        if (! preg_match(
            '/^\d{1,8}(\.\d{1,2})?$/',
            $value
        )) {
            return null;
        }

        return $value;
    }

    // ========================================
    // MOSTRAR CATÁLOGO
    // ========================================

    public function render()
    {
        // Categorías habilitadas
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Categoría que estamos visitando
        $currentCategory = $this->categoryId
            ? Category::query()
                ->where('is_active', true)
                ->findOrFail($this->categoryId)
            : null;

        // ====================================
        // OPCIONES DE TALLAS Y COLORES
        // ====================================

        $variantOptions = ProductVariant::query()
            ->where('is_active', true)
            ->whereHas('product', function (Builder $query) use (
                $currentCategory
            ) {
                $query->where('is_active', true)
                    ->whereHas('category', function (Builder $q) {
                        $q->where('is_active', true);
                    });

                if ($currentCategory) {
                    $query->where(
                        'category_id',
                        $currentCategory->id
                    );
                }
            });

        // Tallas registradas en MySQL
        $availableSizes = (clone $variantOptions)
            ->select('size')
            ->distinct()
            ->orderBy('size')
            ->pluck('size');

        // Colores registrados en MySQL
        $availableColors = (clone $variantOptions)
            ->select('color_name')
            ->distinct()
            ->orderBy('color_name')
            ->pluck('color_name');

        // ====================================
        // CONSULTA PRINCIPAL DE PRODUCTOS
        // ====================================

        $query = Product::query()
            ->where('is_active', true)

            ->whereHas('category', function (Builder $q) {
                $q->where('is_active', true);
            })

            ->when($currentCategory, function (Builder $q) use (
                $currentCategory
            ) {
                $q->where(
                    'category_id',
                    $currentCategory->id
                );
            })

            ->when(
                trim($this->search) !== '',
                function (Builder $q) {
                    $q->where(
                        'name',
                        'like',
                        '%'.trim($this->search).'%'
                    );
                }
            )

            ->with([
                'category',
                'variants' => fn ($query) => $query->where('is_active', true),

                'images' => function ($q) {
                    $q->orderByDesc('is_primary')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])

            ->withSum([
                'variants as available_stock' => function ($q) {
                    $q->where('is_active', true);
                },
            ], 'stock');

        // ====================================
        // FILTRAR TALLA, COLOR Y STOCK
        // EN UNA MISMA VARIANTE
        // ====================================

        if (
            $this->size !== '' ||
            $this->color !== '' ||
            $this->onlyInStock
        ) {
            $query->whereHas(
                'variants',
                function (Builder $variantQuery) {

                    $variantQuery->where(
                        'is_active',
                        true
                    );

                    if ($this->size !== '') {
                        $variantQuery->where(
                            'size',
                            $this->size
                        );
                    }

                    if ($this->color !== '') {
                        $variantQuery->where(
                            'color_name',
                            $this->color
                        );
                    }

                    if ($this->onlyInStock) {
                        $variantQuery->where(
                            'stock',
                            '>',
                            0
                        );
                    }
                }
            );
        }

        // ====================================
        // FILTRAR RANGO DE PRECIOS
        // ====================================

        $min = $this->validPrice($this->minPrice);
        $max = $this->validPrice($this->maxPrice);

        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        // ====================================
        // ORDENAMIENTO
        // ====================================

        switch ($this->sort) {
            case 'precio_asc':
                $query->orderBy('price')
                    ->orderBy('id');
                break;

            case 'precio_desc':
                $query->orderByDesc('price')
                    ->orderBy('id');
                break;

            case 'nombre':
                $query->orderBy('name')
                    ->orderBy('id');
                break;

            default:
                $query->latest('id');
                break;
        }

        // Contar filtros activos
        $activeFilters = 0;

        foreach ([
            $this->size,
            $this->color,
            $this->minPrice,
            $this->maxPrice,
        ] as $value) {
            if ($value !== '') {
                $activeFilters++;
            }
        }

        if ($this->onlyInStock) {
            $activeFilters++;
        }

        return view('livewire.store.catalog', [
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'products' => $query->paginate(12),
            'availableSizes' => $availableSizes,
            'availableColors' => $availableColors,
            'activeFilters' => $activeFilters,
        ]);
    }
}
