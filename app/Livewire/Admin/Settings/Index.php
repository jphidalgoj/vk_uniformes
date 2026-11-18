<?php

namespace App\Livewire\Admin\Settings;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Index extends Component
{
    public string $businessName = 'VK Uniformes';

    public string $whatsappNumber = '';

    public string $contactEmail = '';

    public string $city = '';

    public string $address = '';

    public string $notice = '';

    // ========================================
    // CARGAR CONFIGURACIÓN
    // ========================================

    public function mount(): void
    {
        Gate::authorize('access-admin');

        $settings = StoreSetting::query()
            ->firstOrCreate(
                ['key' => 'main'],
                [
                    'business_name' => 'VK Uniformes',
                ]
            );

        $this->businessName =
            $settings->business_name;

        $this->whatsappNumber =
            $settings->whatsapp_number ?? '';

        $this->contactEmail =
            $settings->contact_email ?? '';

        $this->city =
            $settings->city ?? '';

        $this->address =
            $settings->address ?? '';
    }

    // ========================================
    // GUARDAR CONFIGURACIÓN
    // ========================================

    public function save(): void
    {
        Gate::authorize('access-admin');

        $this->businessName = trim(
            $this->businessName
        );

        $this->whatsappNumber = trim(
            $this->whatsappNumber
        );

        $this->contactEmail = trim(
            $this->contactEmail
        );

        $this->city = trim($this->city);

        $this->address = trim($this->address);

        // El número debe incluir código de país
        // y contener exclusivamente dígitos.
        $this->validate([
            'businessName' => [
                'required',
                'string',
                'max:120',
            ],

            'whatsappNumber' => [
                'nullable',
                'regex:/^[1-9][0-9]{7,14}$/',
            ],

            'contactEmail' => [
                'nullable',
                'email',
                'max:180',
            ],

            'city' => [
                'nullable',
                'string',
                'max:120',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'businessName.required' => 'Ingresa el nombre del negocio.',

            'whatsappNumber.regex' => 'Ingresa un número internacional válido, sin + ni espacios.',

            'contactEmail.email' => 'Ingresa un correo electrónico válido.',
        ]);

        StoreSetting::query()->updateOrCreate(
            ['key' => 'main'],
            [
                'business_name' => $this->businessName,

                'whatsapp_number' => $this->whatsappNumber !== ''
                        ? $this->whatsappNumber
                        : null,

                'contact_email' => $this->contactEmail !== ''
                        ? $this->contactEmail
                        : null,

                'city' => $this->city !== ''
                        ? $this->city
                        : null,

                'address' => $this->address !== ''
                        ? $this->address
                        : null,
            ]
        );

        $this->notice =
            'Configuración actualizada correctamente.';
    }

    // ========================================
    // VISTA
    // ========================================

    public function render()
    {
        return view(
            'livewire.admin.settings.index'
        );
    }
}
