<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class StoreFooter extends Component
{
    public string $businessName;

    public array $site = [];

    public ?string $contactEmail;

    public ?string $city;

    public ?string $address;

    public ?string $whatsappUrl;

    public Collection $categories;

    public function __construct()
    {
        // =====================================
        // INFORMACIÓN COMERCIAL
        // =====================================

        $settings = StoreSetting::query()
            ->where('key', 'main')
            ->first();
        $this->site = $settings?->storefront ?? [];

        $this->businessName =
            $settings?->business_name
            ?: 'VK Uniformes';

        $this->contactEmail =
            $settings?->contact_email;

        $this->city =
            $settings?->city;

        $this->address =
            $settings?->address;

        // =====================================
        // NÚMERO DE WHATSAPP
        // =====================================

        $phone = trim(
            (string) (
                $settings?->whatsapp_number ?? ''
            )
        );

        $this->whatsappUrl = null;

        // Solo aceptar números internacionales
        // configurados correctamente.
        if (
            preg_match(
                '/^[1-9][0-9]{7,14}$/',
                $phone
            )
        ) {
            $message = implode("\n", [
                'Hola, '.$this->businessName.'.',
                '',
                'Quisiera recibir información sobre sus uniformes profesionales.',
                '',
                '¿Podrían ayudarme?',
            ]);

            $this->whatsappUrl =
                'https://wa.me/'
                .$phone
                .'?text='
                .rawurlencode($message);
        }

        // =====================================
        // CATEGORÍAS DEL FOOTER
        // =====================================

        $this->categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get([
                'id',
                'name',
                'slug',
            ]);
    }

    public function render(): View
    {
        return view('components.store-footer');
    }
}
