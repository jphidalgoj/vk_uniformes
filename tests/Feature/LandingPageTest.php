<?php

use App\Livewire\Admin\Banners\Edit;
use App\Livewire\Admin\Landing\Index;
use App\Models\Banner;
use App\Models\StoreSetting;
use App\Models\User;
use Database\Factories\ProductFactory;
use Database\Seeders\LandingPageSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
});

test('landing displays selected active products and hides disabled content', function () {
    $visible = ProductFactory::new()->create(['name' => 'Uniforme visible']);
    $hidden = ProductFactory::new()->create(['name' => 'Uniforme oculto', 'is_active' => false]);
    $hiddenCategory = ProductFactory::new()->create(['name' => 'Categoría oculta']);
    $hiddenCategory->category->update(['is_active' => false]);
    Banner::create(['key' => 'home-test', 'title' => 'Colección clínica', 'is_active' => true, 'content' => ['type' => 'products', 'product_ids' => [$visible->id, $hidden->id, $hiddenCategory->id]]]);
    Banner::create(['key' => 'home-hidden', 'title' => 'Campaña oculta', 'is_active' => false]);
    $this->get('/')->assertOk()->assertSee('Uniforme visible')->assertDontSee('Uniforme oculto')->assertDontSee('Categoría oculta')->assertDontSee('Campaña oculta');
});

test('only administrators can edit landing content', function () {
    $this->get('/admin/portada')->assertRedirect('/admin/login');
    $this->actingAs(User::factory()->create(['is_admin' => false]));
    $this->get('/admin/portada')->assertForbidden();
    Livewire::test(Index::class)->assertForbidden();
});

test('administrator can save a section with products and an uploaded image', function () {
    Storage::fake('public');
    $product = ProductFactory::new()->create();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->set('title', 'Nueva colección')->set('type', 'products')
        ->set('productIds', [$product->id])->set('image', UploadedFile::fake()->image('banner.jpg'))
        ->set('sortOrder', 25)->call('save')->assertHasNoErrors();
    $section = Banner::where('title', 'Nueva colección')->firstOrFail();
    expect($section->content['product_ids'])->toBe([$product->id]);
    Storage::disk('public')->assertExists($section->image_path);
    $this->get('/')->assertSee('Nueva colección')->assertSee($product->name);
});

test('invalid destinations media and product selections cannot be saved', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->set('title', 'Campaña')->set('buttonUrl', '//evil.example')
        ->set('asset', '../.env')->set('productIds', [999999])->set('videoUrl', 'javascript:alert(1)')
        ->call('save')->assertHasErrors(['buttonUrl', 'asset', 'productIds.0', 'videoUrl']);
    $this->assertDatabaseCount('banners', 0);
});

test('header and footer changes persist and reject unsafe links', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->set('site.announcement', 'Atención a empresas')
        ->set('site.hours', 'Lunes a viernes')->call('saveSite')->assertHasNoErrors();
    $this->get('/')->assertOk()->assertSee('Atención a empresas')->assertSee('Lunes a viernes');
    Livewire::test(Index::class)->set('site.instagram', 'javascript:alert(1)')->call('saveSite')->assertHasErrors('site.instagram');
    expect(StoreSetting::first()->storefront['instagram'])->toBe('');
});

test('seeding preserves administrator edits and includes all three supplied sliders', function () {
    $this->seed(LandingPageSeeder::class);
    Banner::where('key', 'home-ribknit')->update(['title' => 'Título personalizado', 'is_active' => false]);
    $this->seed(LandingPageSeeder::class);
    expect(Banner::count())->toBe(12);
    expect(Banner::where('key', 'home-ribknit')->first()->title)->toBe('Título personalizado');
    expect(Banner::where('key', 'home-ribknit')->first()->is_active)->toBeFalse();
    expect(Banner::all()->filter(fn ($section) => str_starts_with($section->content['asset'] ?? '', 'slider/')))->toHaveCount(3);
});

test('hero accepts four images and preserves them when editing other fields', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $editor = Livewire::test(Index::class)->set('title', 'Carrusel de prueba')->set('type', 'hero');
    for ($index = 0; $index < 4; $index++) {
        $editor->set('slideUploads.'.$index, UploadedFile::fake()->image('slide-'.$index.'.jpg'));
        $editor->set('slideAlts.'.$index, 'Diapositiva '.($index + 1));
    }
    $editor->call('save')->assertHasNoErrors();
    $section = Banner::where('title', 'Carrusel de prueba')->firstOrFail();
    expect($section->slides())->toHaveCount(4);
    foreach ($section->slides() as $slide) {
        Storage::disk('public')->assertExists($slide['path']);
    }
    Livewire::test(Index::class)->call('edit', $section->id)->set('title', 'Carrusel actualizado')->call('save')->assertHasNoErrors();
    expect($section->fresh()->slides())->toBe($section->slides());
    $this->get('/')->assertOk()->assertSee('data-carousel', false)->assertSee('Diapositiva 4');
});

test('hero rejects a fifth image and invalid file formats', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->set('title', 'Carrusel')->set('type', 'hero')
        ->set('slideAssets', array_fill(0, 5, 'images/logo-vk.png'))->call('save')->assertHasErrors('slideAssets');
    Livewire::test(Index::class)->set('title', 'Carrusel')->set('type', 'hero')
        ->set('slideUploads.0', UploadedFile::fake()->create('document.pdf', 20, 'application/pdf'))
        ->call('save')->assertHasErrors('slideUploads.0');
    $this->assertDatabaseCount('banners', 0);
});

test('hero can remove a slide without affecting remaining images', function () {
    $this->seed(LandingPageSeeder::class);
    $hero = Banner::where('key', 'home-main')->firstOrFail();
    $original = $hero->slides();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Index::class)->call('edit', $hero->id)->set('removeSlides.1', true)->call('save')->assertHasNoErrors();
    expect($hero->fresh()->slides())->toBe([$original[0], $original[2]]);
    $this->seed(LandingPageSeeder::class);
    expect($hero->fresh()->slides())->toHaveCount(2);
});

test('the existing banner editor also updates the first carousel image', function () {
    Storage::fake('public');
    $this->seed(LandingPageSeeder::class);
    $hero = Banner::where('key', 'home-main')->firstOrFail();
    $remaining = array_slice($hero->slides(), 1);
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(Edit::class)
        ->set('image', UploadedFile::fake()->image('replacement.jpg'))
        ->set('imageAlt', 'Portada reemplazada')->call('save')->assertHasNoErrors();
    $hero->refresh();
    expect($hero->slides()[0]['path'])->toBe($hero->image_path);
    expect($hero->slides()[0]['alt'])->toBe('Portada reemplazada');
    expect(array_slice($hero->slides(), 1))->toBe($remaining);
    Storage::disk('public')->assertExists($hero->image_path);
});

test('administrator can delete a section from landing', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $section = Banner::create([
        'key' => 'home-to-delete',
        'title' => 'Sección para borrar',
        'is_active' => true,
        'image_path' => 'banners/dummy.jpg',
        'content' => ['type' => 'banner'],
    ]);
    Storage::disk('public')->put('banners/dummy.jpg', 'content');

    Livewire::test(Index::class)
        ->call('delete', $section->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('banners', ['id' => $section->id]);
    Storage::disk('public')->assertMissing('banners/dummy.jpg');
});

test('administrator can create a split section with text and image', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(Index::class)
        ->set('title', 'Innovación textil para el sector salud')
        ->set('type', 'split')
        ->set('description', 'Innovamos para quienes dedican su vida a cuidar la de los demás.')
        ->set('highlightText', '¡SOMOS INTELIGENCIA TEXTIL QUE PROTEGE LA VIDA!')
        ->set('imagePosition', 'left')
        ->set('image', UploadedFile::fake()->image('split.jpg'))
        ->set('buttonText', 'Ver catálogo')
        ->set('buttonUrl', '/catalogo')
        ->call('save')
        ->assertHasNoErrors();

    $section = Banner::where('title', 'Innovación textil para el sector salud')->firstOrFail();
    expect($section->content['type'])->toBe('split');
    expect($section->content['highlight_text'])->toBe('¡SOMOS INTELIGENCIA TEXTIL QUE PROTEGE LA VIDA!');
    expect($section->content['image_position'])->toBe('left');
    Storage::disk('public')->assertExists($section->image_path);

    $this->get('/')
        ->assertOk()
        ->assertSee('Innovación textil para el sector salud')
        ->assertSee('¡SOMOS INTELIGENCIA TEXTIL QUE PROTEGE LA VIDA!')
        ->assertSee('Ver catálogo');
});

test('administrator can configure up to 3 videos with continuous playback', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(Index::class)
        ->set('title', 'Videos demostrativos')
        ->set('type', 'video')
        ->set('videoUrls.0', 'https://example.com/video1.mp4')
        ->set('videoTitles.0', 'Demostración 1')
        ->set('videoUrls.1', 'https://example.com/video2.mp4')
        ->set('videoTitles.1', 'Demostración 2')
        ->call('save')
        ->assertHasNoErrors();

    $section = Banner::where('title', 'Videos demostrativos')->firstOrFail();
    expect($section->videos())->toHaveCount(2);
    expect($section->videos()[0]['url'])->toBe('https://example.com/video1.mp4');
    expect($section->videos()[1]['url'])->toBe('https://example.com/video2.mp4');

    $this->get('/')
        ->assertOk()
        ->assertSee('Videos demostrativos')
        ->assertSee('https://example.com/video1.mp4')
        ->assertSee('https://example.com/video2.mp4');
});

test('administrator can configure a showcase section with large image and product grid', function () {
    Storage::fake('public');
    $product1 = ProductFactory::new()->create(['name' => 'Filipina Quirúrgica Stretch']);
    $product2 = ProductFactory::new()->create(['name' => 'Pantalón Clínico Jogger']);
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(Index::class)
        ->set('title', 'Colección Exclusiva Médica')
        ->set('type', 'showcase')
        ->set('imagePosition', 'right')
        ->set('productIds', [$product1->id, $product2->id])
        ->set('image', UploadedFile::fake()->image('showcase-model.jpg'))
        ->set('buttonText', 'Ver toda la colección')
        ->set('buttonUrl', '/catalogo')
        ->call('save')
        ->assertHasNoErrors();

    $section = Banner::where('title', 'Colección Exclusiva Médica')->firstOrFail();
    expect($section->content['type'])->toBe('showcase');
    expect($section->content['image_position'])->toBe('right');
    expect($section->content['product_ids'])->toBe([$product1->id, $product2->id]);
    Storage::disk('public')->assertExists($section->image_path);

    $this->get('/')
        ->assertOk()
        ->assertSee('Colección Exclusiva Médica')
        ->assertSee('Filipina Quirúrgica Stretch')
        ->assertSee('Pantalón Clínico Jogger')
        ->assertSee('Ver toda la colección');
});

test('hero slide can have individual category destination url', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    Livewire::test(Index::class)
        ->set('title', 'Carrusel con enlaces por slide')
        ->set('type', 'hero')
        ->set('slideUploads.0', UploadedFile::fake()->image('slide1.jpg'))
        ->set('slideAlts.0', 'Odontopediatría')
        ->set('slideUrls.0', '/catalogo/odontopediatria')
        ->call('save')
        ->assertHasNoErrors();

    $section = Banner::where('title', 'Carrusel con enlaces por slide')->firstOrFail();
    $slides = $section->slides();
    expect($slides[0]['url'])->toBe('/catalogo/odontopediatria');

    $slideImages = $section->slideImages();
    expect($slideImages[0]['destination'])->toBe(url('/catalogo/odontopediatria'));

    $this->get('/')
        ->assertOk()
        ->assertSee(url('/catalogo/odontopediatria'));
});

test('editing section with a deleted product cleans it automatically', function () {
    $activeProduct = ProductFactory::new()->create(['name' => 'Uniforme Activo']);
    $deletedProduct = ProductFactory::new()->create(['name' => 'Uniforme Borrado']);
    $deletedProduct->delete();

    $banner = Banner::create([
        'key' => 'home-cleanup-test',
        'title' => 'Sección con producto borrado',
        'is_active' => true,
        'content' => [
            'type' => 'products',
            'product_ids' => [$deletedProduct->id, $activeProduct->id],
        ],
    ]);

    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $editor = Livewire::test(Index::class)
        ->call('edit', $banner->id);

    // Stale deleted product ID is removed automatically
    expect($editor->get('productIds'))->toBe([$activeProduct->id]);

    $editor->call('save')->assertHasNoErrors();
    expect($banner->fresh()->content['product_ids'])->toBe([$activeProduct->id]);
});
