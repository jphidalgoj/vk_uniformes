<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Admin\Banners\Edit as BannerEdit;
use App\Livewire\Admin\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Landing\Index;
use App\Livewire\Admin\Products\ImageGallery;
use App\Livewire\Admin\Products\Index as ProductIndex;
use App\Livewire\Admin\Settings\Index as StoreSettingsIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Store\Catalog;
use App\Livewire\Store\Cookies;
use App\Livewire\Store\Home;
use App\Livewire\Store\Privacy;
use App\Livewire\Store\ProductDetail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| CATÁLOGO PÚBLICO VK UNIFORMES
|--------------------------------------------------------------------------
*/

// Catálogo completo
Route::livewire('/catalogo', Catalog::class)
    ->name('store.catalog');

// Catálogo filtrado por categoría
Route::livewire('/catalogo/{categorySlug}', Catalog::class)
    ->name('store.category');

// Detalle individual del uniforme
Route::livewire('/producto/{slug}', ProductDetail::class)
    ->name('store.product');

// Páginas legales
Route::livewire('/privacidad', Privacy::class)
    ->name('store.privacy');

Route::livewire('/cookies', Cookies::class)
    ->name('store.cookies');

// Sitemap para Google Search Console
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Servir archivos multimedia de storage con soporte de streaming (video/mp4 y byte-ranges)
Route::get('/storage/{path}', function (string $path) {
    $decodedPath = ltrim(urldecode($path), '/');
    $disk = Storage::disk('public');

    if ($disk->exists($decodedPath)) {
        $fullPath = $disk->path($decodedPath);
        $mimeType = $disk->mimeType($decodedPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    $directPath = storage_path('app/public/'.$decodedPath);
    if (file_exists($directPath)) {
        $mimeType = mime_content_type($directPath) ?: 'application/octet-stream';

        return response()->file($directPath, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    abort(404);
})->where('path', '.*')->name('storage.file');

// =========================================
// TIENDA PÚBLICA
// =========================================

Route::livewire('/', Home::class)
    ->name('home');

// =========================================
// AUTENTICACIÓN
// =========================================

Route::middleware('guest')->group(function () {
    Route::livewire('/admin/login', Login::class)
        ->name('login');
});

// =========================================
// ADMINISTRACIÓN
// =========================================

Route::middleware(['auth', 'can:access-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::livewire('/dashboard', Dashboard::class)
            ->name('dashboard');

        Route::livewire('/categories', CategoryIndex::class)
            ->name('categories.index');

        Route::post('/logout', LogoutController::class)
            ->name('logout');

        Route::livewire('/products', ProductIndex::class)
            ->name('products.index');

        Route::livewire(
            '/products/{product}/images',
            ImageGallery::class
        )->name('products.images');

        // Administración del banner principal
        Route::livewire('/banners/home', BannerEdit::class)
            ->name('banners.home');

        Route::livewire('/settings', StoreSettingsIndex::class)
            ->name('settings.index');

        Route::livewire('/portada', Index::class)
            ->name('landing.index');
    });
