<?php

namespace App\Livewire\Store;

use App\Models\StoreSetting;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Cookies extends Component
{
    public function render(): View
    {
        $businessName = StoreSetting::where('key', 'main')->value('business_name') ?: 'VK Uniformes';

        return view('livewire.store.cookies', [
            'businessName' => $businessName,
        ])->title($businessName.' | Política de cookies');
    }
}
