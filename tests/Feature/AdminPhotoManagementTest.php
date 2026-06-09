<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\House;
use App\Models\Photo;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPhotoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_image_for_house(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'houses', 'id' => $house->id]), [
                'images' => [$this->fakeImage('house.jpg')],
                'alt' => 'Fachada da casa',
            ])
            ->assertRedirect();

        $photo = $house->photos()->firstOrFail();

        $this->assertSame('Fachada da casa', $photo->alt);
        $this->assertTrue($photo->is_cover);
        $this->assertStringStartsWith('houses/', $photo->path);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_admin_can_upload_image_for_rental_unit(): void
    {
        Storage::fake('public');
        $unit = $this->createRentalUnit();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'rental-units', 'id' => $unit->id]), [
                'images' => [$this->fakeImage('unit.webp')],
                'alt' => 'Sala da unidade',
            ])
            ->assertRedirect();

        $photo = $unit->photos()->firstOrFail();

        $this->assertSame('Sala da unidade', $photo->alt);
        $this->assertTrue($photo->is_cover);
        $this->assertStringStartsWith('rental-units/', $photo->path);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_admin_can_upload_image_for_activity(): void
    {
        Storage::fake('public');
        $activity = $this->createActivity();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'activities', 'id' => $activity->id]), [
                'images' => [$this->fakeImage('activity.png')],
                'alt' => 'Trilho na serra',
            ])
            ->assertRedirect();

        $photo = $activity->photos()->firstOrFail();

        $this->assertSame('Trilho na serra', $photo->alt);
        $this->assertTrue($photo->is_cover);
        $this->assertStringStartsWith('activities/', $photo->path);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_guest_cannot_upload_image(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();

        $this
            ->post(route('admin.photos.store', ['type' => 'houses', 'id' => $house->id]), [
                'images' => [$this->fakeImage('house.jpg')],
            ])
            ->assertRedirect('/login');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_invalid_file_is_rejected(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'houses', 'id' => $house->id]), [
                'images' => [UploadedFile::fake()->create('icon.svg', 10, 'image/svg+xml')],
            ])
            ->assertSessionHasErrors('images.0');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_oversized_file_is_rejected(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'houses', 'id' => $house->id]), [
                'images' => [UploadedFile::fake()->create('large.jpg', 4097, 'image/jpeg')],
            ])
            ->assertSessionHasErrors('images.0');

        $this->assertDatabaseCount('photos', 0);
    }

    public function test_first_uploaded_image_becomes_cover_automatically(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();

        $this
            ->actingAsAdmin()
            ->post(route('admin.photos.store', ['type' => 'houses', 'id' => $house->id]), [
                'images' => [
                    $this->fakeImage('first.jpg'),
                    $this->fakeImage('second.jpg'),
                ],
            ])
            ->assertRedirect();

        $photos = $house->photos()->orderBy('sort_order')->get();

        $this->assertTrue($photos[0]->is_cover);
        $this->assertFalse($photos[1]->is_cover);
    }

    public function test_setting_cover_removes_cover_from_other_images(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();
        $first = $this->createStoredPhoto($house, 'houses/first.jpg', ['is_cover' => true]);
        $second = $this->createStoredPhoto($house, 'houses/second.jpg', ['is_cover' => false]);

        $this
            ->actingAsAdmin()
            ->patch(route('admin.photos.cover', $second))
            ->assertRedirect();

        $this->assertFalse($first->fresh()->is_cover);
        $this->assertTrue($second->fresh()->is_cover);
    }

    public function test_deleting_image_removes_record_and_file(): void
    {
        Storage::fake('public');
        $house = $this->createHouse();
        $photo = $this->createStoredPhoto($house, 'houses/delete-me.jpg', ['is_cover' => true]);

        $this
            ->actingAsAdmin()
            ->delete(route('admin.photos.destroy', $photo))
            ->assertRedirect();

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('houses/delete-me.jpg');
    }

    public function test_public_page_uses_cover_image_when_it_exists(): void
    {
        $house = $this->createHouse();
        $house->photos()->create([
            'path' => 'houses/cover.jpg',
            'alt' => 'Capa publica',
            'sort_order' => 0,
            'is_cover' => true,
        ]);

        $this
            ->get(route('houses.show', $house))
            ->assertOk()
            ->assertSee('/storage/houses/cover.jpg', false)
            ->assertSee('Capa publica');
    }

    public function test_public_placeholder_appears_when_there_is_no_image(): void
    {
        $this->createHouse([
            'name' => 'Casa Sem Imagem',
            'slug' => 'casa-sem-imagem',
        ]);

        $this
            ->get(route('houses.index'))
            ->assertOk()
            ->assertSee('aria-label="Sem imagem"', false);
    }

    private function createHouse(array $overrides = []): House
    {
        return House::query()->create(array_merge([
            'name' => 'Casa Teste',
            'slug' => 'casa-teste',
            'short_description' => 'Casa de teste.',
            'description' => 'Casa criada para testes de imagem.',
            'location' => 'Geres',
            'is_active' => true,
            'featured' => false,
        ], $overrides));
    }

    private function createRentalUnit(array $overrides = []): RentalUnit
    {
        $house = $this->createHouse();

        return $house->rentalUnits()->create(array_merge([
            'name' => 'T1 Teste',
            'slug' => 't1-teste',
            'type' => 'T1',
            'short_description' => 'Unidade de teste.',
            'description' => 'Unidade criada para testes de imagem.',
            'capacity' => 2,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'base_price' => 80,
            'rules' => null,
            'is_active' => true,
            'featured' => false,
        ], $overrides));
    }

    private function createActivity(array $overrides = []): Activity
    {
        return Activity::query()->create(array_merge([
            'title' => 'Atividade Teste',
            'slug' => 'atividade-teste',
            'category' => 'Trilhos',
            'description' => 'Atividade criada para testes de imagem.',
            'location' => 'Geres',
            'distance' => '10 min',
            'image' => null,
            'is_featured' => false,
            'is_active' => true,
        ], $overrides));
    }

    private function createStoredPhoto(House $house, string $path, array $overrides = []): Photo
    {
        Storage::disk('public')->put($path, 'fake-image');

        return $house->photos()->create(array_merge([
            'path' => $path,
            'alt' => 'Imagem teste',
            'sort_order' => 0,
            'is_cover' => false,
        ], $overrides));
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::factory()->create());
    }

    private function fakeImage(string $name): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=');

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
