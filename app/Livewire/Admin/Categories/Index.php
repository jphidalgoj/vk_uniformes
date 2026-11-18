<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Búsqueda y filtros
    public string $search = '';

    public string $status = 'all';

    // Estado del formulario
    public bool $showForm = false;

    public ?int $editingId = null;

    // Campos de la categoría
    public string $name = '';

    public string $description = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    // Gestión de imágenes
    public $image = null;

    public ?string $currentImage = null;

    public bool $removeImage = false;

    // Notificaciones
    public string $notice = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    // ========================================
    // CREAR CATEGORÍA
    // ========================================
    public function create(): void
    {
        Gate::authorize('access-admin');

        $this->resetForm();

        $this->sortOrder = (int) Category::max('sort_order') + 1;

        $this->showForm = true;
    }

    // ========================================
    // EDITAR CATEGORÍA
    // ========================================
    public function edit(int $id): void
    {
        Gate::authorize('access-admin');

        $category = Category::findOrFail($id);

        $this->resetForm();

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->sortOrder = $category->sort_order;
        $this->isActive = $category->is_active;
        $this->currentImage = $category->image_path;

        $this->showForm = true;
    }

    // ========================================
    // GUARDAR CATEGORÍA
    // ========================================
    public function save(): void
    {
        Gate::authorize('access-admin');

        $this->name = trim($this->name);

        $nameRule = Rule::unique('categories', 'name');

        if ($this->editingId !== null) {
            $nameRule->ignore($this->editingId);
        }

        $this->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                $nameRule,
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'sortOrder' => [
                'required',
                'integer',
                'min:0',
                'max:9999',
            ],
            'isActive' => [
                'boolean',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],
        ], [
            'name.required' => 'Ingresa el nombre de la categoría.',
            'name.unique' => 'Ya existe una categoría con este nombre.',
            'name.max' => 'El nombre no puede superar 120 caracteres.',
            'description.max' => 'La descripción es demasiado larga.',
            'image.image' => 'Selecciona una imagen válida.',
            'image.mimes' => 'Utiliza JPG, PNG o WebP.',
            'image.max' => 'La imagen no puede superar 3 MB.',
        ]);

        $isEditing = $this->editingId !== null;

        $category = $isEditing
            ? Category::findOrFail($this->editingId)
            : new Category;

        // El slug se genera al crear.
        // Al editar se conserva para evitar
        // cambiar las URLs de las categorías.
        if (! $isEditing) {
            $slug = Str::slug($this->name);

            if (
                $slug === '' ||
                Category::where('slug', $slug)->exists()
            ) {
                throw ValidationException::withMessages([
                    'name' => 'El nombre genera una URL duplicada o inválida.',
                ]);
            }

            $category->slug = $slug;
        }

        $oldImage = $category->image_path;
        $newImage = $oldImage;

        if ($this->image) {
            $newImage = $this->image->store(
                'categories',
                'public'
            );
        } elseif ($this->removeImage) {
            $newImage = null;
        }

        try {
            $category->name = $this->name;

            $category->description =
                trim($this->description) ?: null;

            $category->sort_order = $this->sortOrder;
            $category->is_active = $this->isActive;
            $category->image_path = $newImage;

            $category->save();
        } catch (\Throwable $exception) {
            // Evita dejar una nueva imagen sin registro
            // si falla el guardado en MySQL.
            if ($newImage && $newImage !== $oldImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        // Eliminar imagen anterior solamente
        // después de guardar correctamente.
        if ($oldImage && $oldImage !== $newImage) {
            Storage::disk('public')->delete($oldImage);
        }

        $this->notice = $isEditing
            ? 'Categoría actualizada correctamente.'
            : 'Categoría creada correctamente.';

        $this->cancelForm();
        $this->resetPage();
    }

    // ========================================
    // ACTIVAR O DESACTIVAR
    // ========================================
    public function toggleActive(int $id): void
    {
        Gate::authorize('access-admin');

        $category = Category::findOrFail($id);

        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        $this->notice = 'Visibilidad actualizada correctamente.';
    }

    // ========================================
    // ELIMINAR CATEGORÍA
    // ========================================
    public function delete(int $id): void
    {
        Gate::authorize('access-admin');

        $category = Category::findOrFail($id);

        $imagePath = $category->image_path;

        $category->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($this->editingId === $id) {
            $this->cancelForm();
        }

        $this->notice = 'Categoría eliminada correctamente.';
        $this->resetPage();
    }

    // ========================================
    // CANCELAR FORMULARIO
    // ========================================
    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'description',
            'sortOrder',
            'isActive',
            'image',
            'currentImage',
            'removeImage',
        ]);

        $this->resetValidation();
    }

    // ========================================
    // MOSTRAR VISTA
    // ========================================
    public function render()
    {
        $categories = Category::query()
            ->when($this->search !== '', function ($query) {
                $query->where(
                    'name',
                    'like',
                    '%'.trim($this->search).'%'
                );
            })
            ->when($this->status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(8);

        return view('livewire.admin.categories.index', [
            'categories' => $categories,
            'totalCategories' => Category::count(),
            'activeCategories' => Category::where(
                'is_active',
                true
            )->count(),
        ]);
    }
}
