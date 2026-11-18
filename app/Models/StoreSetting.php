<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'key',
        'business_name',
        'whatsapp_number',
        'contact_email',
        'city',
        'address',
        'storefront',
    ];

    protected function casts(): array
    {
        return ['storefront' => 'array'];
    }
}
