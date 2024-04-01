<?php

use App\Models\Image;
use App\Models\Place;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('returns not found if image do not exist for this place', function () {
    $firstPlace = Place::factory()->invisible()->hasImages(1)->create();
    $secondPlace = Place::factory()->invisible()->create();
    $secondPlaceImage = Image::factory()->for($secondPlace, 'imageable')->create();

    delete(route('admin.posts.images.destroy', [$firstPlace, $secondPlaceImage->id]))
        ->assertStatus(404);
});

it('can delete a place image', function () {
    $place = Place::factory()->invisible()->create();
    $image = UploadedFile::fake()->image('testImage');
    $image->storeAs('uploads/places/', $image->hashName());
    $imagePath = 'uploads/places/'.$image->hashName();
    $placeImage = Image::factory()->is_main()->for($place, 'imageable')->create([
        'path' => $imagePath
    ]);

    delete(route('admin.places.images.destroy', [$place, $placeImage->id]))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('places.image.destroy')
        ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Place::class,
        'imageable_id' => $place->id,
        'path' => $imagePath
    ]);

    Storage::assertMissing($imagePath);
});

it('can not delete a place main image if there are other images attached', function () {
    $place = Place::factory()->invisible()->create();
    Image::factory()->count(2)->for($place, 'imageable')->sequence(
        ['is_main' => true],
        ['is_main' => false]
    )->create();

    delete(route('admin.posts.images.destroy', [$place, $place->images()->first()]))
        ->assertStatus(404);
});
