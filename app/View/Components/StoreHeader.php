<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class StoreHeader extends Component
{
    public Collection $categories;

    public string $businessName = 'VK Uniformes';

    public array $site = [];

    public function __construct()
    {
        $this->site = StoreSetting::where('key', 'main')->first()?->storefront ?? [];

        $this->businessName = StoreSetting::query()
            ->where('key', 'main')
            ->value('business_name') ?: 'VK Uniformes';
        // Categorías visibles y ordenadas
        // desde el panel administrativo.
        $this->categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);
    }

    public function render(): View
    {
        return view('components.store-header');
    }
}
