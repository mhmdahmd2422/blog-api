<?php

use App\Models\Image;
use App\Models\Place;
use App\Models\Specification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('can destroy a specification', function () {
    $specification = Specification::factory()->hasAttached(
        Place::factory()->count(3), ['description' => 'value']
    )->create();
    $image = UploadedFile::fake()->image('testImage.png');
    $imagePath = 'uploads/specifications/' . $image->hashName();
    Image::factory()->for($specification, 'imageable')->create([
        'path' => $imagePath
    ]);

    delete(route('admin.specifications.destroy', $specification))
        ->assertStatus(200)
        ->assertExactJson([
                'message' => __('specifications.destroy')
            ]
        );

    $this->assertDatabaseMissing(Specification::class, [
        'id' => $specification->id,
        'name' => $specification->name
    ]);

    foreach ($specification->places as $place) {
        $this->assertDatabaseMissing('place_specification', [
            'place_id' => $place->id,
            'specification_id' => $specification->id,
        ]);
    }

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Specification::class,
        'imageable_id' => $specification->id,
        'path' => $imagePath
    ]);

    Storage::assertMissing($imagePath);
});
