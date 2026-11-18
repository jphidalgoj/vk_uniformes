<?php

namespace App\Livewire\Store;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Home extends Component
{
    public function render(): View
    {
        $sections = Banner::query()->where('key', 'like', 'home-%')->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $ids = $sections->flatMap(fn (Banner $section): array => $section->content['product_ids'] ?? [])->unique();
        $products = Product::query()->whereIn('id', $ids)->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->with(['category', 'images', 'variants' => fn ($query) => $query->where('is_active', true)])
            ->withSum(['variants as available_stock' => fn ($query) => $query->where('is_active', true)], 'stock')->get()->keyBy('id');

        return view('livewire.store.home', [
            'sections' => $sections,
            'products' => $products,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'businessName' => StoreSetting::where('key', 'main')->value('business_name') ?: 'VK Uniformes',
        ])->title((StoreSetting::where('key', 'main')->value('business_name') ?: 'VK Uniformes').' | Uniformes profesionales');
    }
}
