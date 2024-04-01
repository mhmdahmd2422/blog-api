<?php

use App\Models\Image;
use App\Models\Place;
use App\Models\Specification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('can destroy a place', function () {
    $place = Place::factory()->invisible()->hasImages(3)->hasTags(2)
        ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
        ->create();
    $image = UploadedFile::fake()->image('testImage.png');
    $imagePath = 'uploads/specifications/' . $image->hashName();
    Image::factory()->for($place, 'imageable')->create([
        'path' => $imagePath
    ]);

    delete(route('admin.places.destroy', $place))
        ->assertStatus(200)
        ->assertExactJson([
                'message' => __('places.destroy')
            ]
        );

    $this->assertDatabaseMissing(Place::class, [
        'name' => $place->name,
        'slug' => $place->slug,
        'description' => $place->description,
        'is_visible' => $place->is_visible
    ]);

    foreach ($place->specifications as $specification) {
        $this->assertDatabaseHas('place_specification', [
            'place_id' => $place->id,
            'specification_id' => $specification->id,
        ]);
    }

    foreach ($place->tags as $tag) {
        $this->assertDatabaseMissing('taggables', [
            'tag_id' => $tag->id,
            'taggable_type' => Place::class,
            'taggable_id' => $place->id,
        ]);
    }

    foreach ($place->images as $image) {
        $this->assertDatabaseMissing(Image::class, [
            'imageable_type' => Place::class,
            'imageable_id' => $place->id,
            'path' => $image->path
        ]);

        Storage::assertMissing($image->path);
    }
});
