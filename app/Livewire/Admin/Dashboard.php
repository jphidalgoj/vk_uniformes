<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalCategories' => Category::count(),

            'activeCategories' => Category::query()
                ->where('is_active', true)
                ->count(),
        ]);
    }
}
