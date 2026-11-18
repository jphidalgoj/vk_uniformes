<?php

use App\Livewire\Admin\Products\ImageGallery;
use App\Livewire\Admin\Products\Index;
use App\Livewire\Store\ProductDetail;
use App\Models\User;
use Database\Factories\ProductFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
});

test('administrator uploads multiple photos with a product color and reassigns existing photos', function () {
    Storage::fake('public');
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'blue-m', 'size' => 'M', 'color_name' => 'Azul']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(ImageGallery::class, ['product' => $product])->set('photoColor', 'Azul')
        ->set('photos', [UploadedFile::fake()->image('front.jpg'), UploadedFile::fake()->image('back.jpg')])
        ->call('savePhotos')->assertHasNoErrors();

    expect($product->images()->count())->toBe(2);
    expect($product->images()->where('color_name', 'Azul')->count())->toBe(2);
    expect($product->images()->where('is_primary', true)->count())->toBe(1);
    foreach ($product->images as $image) {
        Storage::disk('public')->assertExists($image->path);
    }

    Livewire::test(ImageGallery::class, ['product' => $product])->call('setPhotoColor', $product->images->first()->id, '')->assertHasNoErrors();
    expect($product->images->first()->fresh()->color_name)->toBeNull();
});

test('uploads reject colors not belonging to the product without storing files', function () {
    Storage::fake('public');
    $product = ProductFactory::new()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(ImageGallery::class, ['product' => $product])->set('photoColor', 'Inventado')
        ->set('photos', [UploadedFile::fake()->image('photo.jpg')])->call('savePhotos')->assertHasErrors('photoColor');

    expect($product->images()->count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('photo color changes cannot target another product or an unknown color', function () {
    $product = ProductFactory::new()->create();
    $other = ProductFactory::new()->create();
    $image = $other->images()->create(['path' => 'other.jpg']);
    $own = $product->images()->create(['path' => 'own.jpg']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(ImageGallery::class, ['product' => $product])->call('setPhotoColor', $image->id, '')->assertNotFound();
    Livewire::test(ImageGallery::class, ['product' => $product])->call('setPhotoColor', $own->id, 'Unknown')->assertHasErrors('color');

    expect($own->fresh()->color_name)->toBeNull();
    expect($image->fresh()->color_name)->toBeNull();
});

test('non administrators cannot manage product photographs', function () {
    $product = ProductFactory::new()->create();
    $this->actingAs(User::factory()->create(['is_admin' => false]));

    Livewire::test(ImageGallery::class, ['product' => $product])->assertForbidden();
});

test('selecting a color changes all gallery photos and clears the selected size', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->createMany([
        ['sku' => 'blue', 'size' => 'M', 'color_name' => 'Azul', 'stock' => 2],
        ['sku' => 'white', 'size' => 'L', 'color_name' => 'Blanco', 'stock' => 3],
    ]);
    $blue = $product->images()->create(['path' => 'blue.jpg', 'color_name' => 'Azul', 'is_primary' => true]);
    $white = $product->images()->create(['path' => 'white.jpg', 'color_name' => 'Blanco']);
    $back = $product->images()->create(['path' => 'white-back.jpg', 'color_name' => 'Blanco']);

    $detail = Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->assertSet('selectedColor', 'Azul')->set('selectedSize', 'M')->call('selectImage', $blue->id)
        ->set('selectedColor', 'Blanco')->assertSet('selectedSize', '')->assertSet('selectedImageId', null)
        ->assertViewHas('images', fn ($images) => $images->modelKeys() === [$white->id, $back->id])
        ->assertViewHas('activeImage', fn ($image) => $image->id === $white->id)
        ->call('selectImage', $back->id)->assertViewHas('activeImage', fn ($image) => $image->id === $back->id);

    $detail->call('selectImage', $blue->id)->assertNotFound();
});

test('colors without their own photos only use general photos', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'red', 'size' => 'M', 'color_name' => 'Rojo']);
    $product->images()->create(['path' => 'blue.jpg', 'color_name' => 'Azul', 'is_primary' => true]);
    $general = $product->images()->create(['path' => 'general.jpg']);

    Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->assertViewHas('images', fn ($images) => $images->modelKeys() === [$general->id])
        ->assertSee('Fotografías de referencia');

    $general->delete();
    Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->assertViewHas('activeImage', null)->assertSee('Todavía no hay fotografías de este color.');
});

test('product links preserve valid colors and ignore invalid query values', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->createMany([
        ['sku' => 'blue', 'size' => 'M', 'color_name' => 'Azul'],
        ['sku' => 'white', 'size' => 'M', 'color_name' => 'Blanco'],
    ]);
    $product->images()->create(['path' => 'blue.jpg', 'color_name' => 'Azul', 'is_primary' => true]);
    $white = $product->images()->create(['path' => 'white.jpg', 'color_name' => 'Blanco']);

    Livewire::withQueryParams(['color' => 'Blanco'])->test(ProductDetail::class, ['slug' => $product->slug])
        ->assertSet('selectedColor', 'Blanco')->assertViewHas('activeImage', fn ($image) => $image->id === $white->id);
    Livewire::withQueryParams(['color' => ['bad']])->test(ProductDetail::class, ['slug' => $product->slug])
        ->assertSet('selectedColor', 'Azul');
});

test('catalog offers only active product colors and retains filters', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->createMany([
        ['sku' => 'blue', 'size' => 'M', 'color_name' => 'Azul', 'stock' => 2],
        ['sku' => 'hidden', 'size' => 'M', 'color_name' => 'Oculto', 'is_active' => false],
    ]);

    $this->get('/catalogo')->assertOk()->assertSee('data-color="Azul"', false)->assertDontSee('data-color="Oculto"', false);
});

test('administrator cannot exceed 4 photos per color', function () {
    Storage::fake('public');
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'scrub-navy', 'size' => 'M', 'color_name' => 'Azul Marino']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $gallery = Livewire::test(ImageGallery::class, ['product' => $product])
        ->set('photoColor', 'Azul Marino')
        ->set('photos', [
            UploadedFile::fake()->image('p1.jpg'),
            UploadedFile::fake()->image('p2.jpg'),
            UploadedFile::fake()->image('p3.jpg'),
            UploadedFile::fake()->image('p4.jpg'),
        ])
        ->call('savePhotos')
        ->assertHasNoErrors();

    expect($product->images()->where('color_name', 'Azul Marino')->count())->toBe(4);

    // Attempting to upload a 5th photo should be rejected with validation error
    $gallery->set('photoColor', 'Azul Marino')
        ->set('photos', [UploadedFile::fake()->image('p5.jpg')])
        ->call('savePhotos')
        ->assertHasErrors('photos');

    expect($product->images()->where('color_name', 'Azul Marino')->count())->toBe(4);
});

test('custom designs accept eight general photos and reject a ninth without extra files', function () {
    Storage::fake('public');
    $product = ProductFactory::new()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $photos = collect(range(1, 8))->map(fn ($number) => UploadedFile::fake()->image("general-{$number}.jpg"))->all();
    $gallery = Livewire::test(ImageGallery::class, ['product' => $product])
        ->set('photos', $photos)->call('savePhotos')->assertHasNoErrors();
    expect($product->images()->whereNull('color_name')->count())->toBe(8);
    expect($product->images()->where('is_primary', true)->count())->toBe(1);
    $gallery->set('photos', [UploadedFile::fake()->image('ninth.jpg')])
        ->call('savePhotos')->assertHasErrors('photos');
    expect($product->images()->count())->toBe(8);
    expect(Storage::disk('public')->allFiles())->toHaveCount(8);
});

test('photo reassignment respects the general gallery limit', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'reassign-blue', 'size' => 'M', 'color_name' => 'Azul']);
    foreach (range(1, 8) as $number) {
        $product->images()->create(['path' => "general-{$number}.jpg"]);
    }
    $photo = $product->images()->create(['path' => 'blue.jpg', 'color_name' => 'Azul']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(ImageGallery::class, ['product' => $product])
        ->call('setPhotoColor', $photo->id, '')->assertHasErrors('color');
    expect($photo->fresh()->color_name)->toBe('Azul');
});

test('administrator saves sizes for a custom design without requiring colors', function () {
    $product = ProductFactory::new()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->call('edit', $product->id)
        ->set('variants', [
            ['sku' => 'custom-m', 'size' => 'M', 'color_name' => '', 'color_hex' => '', 'stock' => 3, 'is_active' => true],
            ['sku' => 'custom-l', 'size' => 'L', 'color_name' => '', 'color_hex' => '', 'stock' => 2, 'is_active' => true],
        ])->call('save')->assertHasNoErrors();
    expect($product->variants()->where('color_name', '')->pluck('size')->all())->toEqualCanonicalizing(['M', 'L']);
});

test('custom design sizes and gallery work without a color and appear in the inquiry', function () {
    config(['vk.whatsapp_number' => '593999999999']);
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'custom-query', 'size' => 'M', 'color_name' => '', 'stock' => 2]);
    foreach (range(1, 8) as $number) {
        $product->images()->create(['path' => "custom-{$number}.jpg"]);
    }
    Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->assertSet('selectedColor', '')->assertViewHas('sizes', fn ($sizes) => $sizes->count() === 1)
        ->assertViewHas('images', fn ($images) => $images->count() === 8)
        ->assertDontSee('aria-label="Selecciona un color"', false)
        ->set('selectedSize', 'M')
        ->assertViewHas('quotationUrl', fn ($url) => str_contains(rawurldecode($url ?? ''), 'Talla: M') && str_contains(rawurldecode($url ?? ''), 'Personalizado'))
        ->assertViewHas('informationUrl', fn ($url) => str_contains(rawurldecode($url ?? ''), 'Talla: M'))
        ->set('selectedSize', 'INVALID')->assertSet('selectedSize', '')->assertViewHas('quotationUrl', null);
});

test('color buttons select valid variants and the inquiry uses the chosen color and size', function () {
    config(['vk.whatsapp_number' => '593999999999']);
    $product = ProductFactory::new()->create();
    $blue = $product->variants()->create(['sku' => 'click-blue', 'size' => 'M', 'color_name' => 'Azul', 'stock' => 2]);
    $white = $product->variants()->create(['sku' => 'click-white', 'size' => 'L', 'color_name' => 'Blanco', 'stock' => 2]);
    Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->call('selectColor', $blue->id)->set('selectedSize', 'M')
        ->call('selectColor', $white->id)->assertSet('selectedSize', '')->assertSet('selectedColor', 'Blanco')
        ->set('selectedSize', 'L')->assertViewHas('quotationUrl', fn ($url) => str_contains(rawurldecode($url ?? ''), 'Blanco') && str_contains(rawurldecode($url ?? ''), 'Talla: L'));
    $other = ProductFactory::new()->create()->variants()->create(['sku' => 'foreign', 'size' => 'M', 'color_name' => 'Rojo']);
    Livewire::test(ProductDetail::class, ['slug' => $product->slug])->call('selectColor', $other->id)->assertNotFound();
});

test('size tile selection updates selectedSize and allows toggling', function () {
    $product = ProductFactory::new()->create();
    $product->variants()->create(['sku' => 'tile-m', 'size' => 'M', 'color_name' => 'Azul', 'stock' => 5]);
    $product->variants()->create(['sku' => 'tile-l', 'size' => 'L', 'color_name' => 'Azul', 'stock' => 0]);

    Livewire::test(ProductDetail::class, ['slug' => $product->slug])
        ->set('selectedColor', 'Azul')
        ->call('selectSize', 'M')
        ->assertSet('selectedSize', 'M')
        ->call('selectSize', 'M')
        ->assertSet('selectedSize', '');
});

test('store configuration emits no bytes that could corrupt Livewire assets', function () {
    ob_start();
    try {
        require config_path('vk.php');
        $output = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    expect($output)->toBe('');
});
