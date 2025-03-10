<?php

use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

it('can store a category with image', function () {
    $category = Category::factory()->invisible()->make();
    $image = UploadedFile::fake()->image('testImage.png');
    $imagePath = 'uploads/categories/' . $image->hashName();

    post(route('admin.categories.store'), [
        'name' => $category->name,
        'image' => $image,
        'is_visible' => $category->is_visible
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make(Category::first()->load('image'))),
            'message' => __('categories.store')
        ]);

    $createdCategory = Category::first();

    $this->assertDatabaseHas(Category::class, [
        'name' => $createdCategory->name,
        'is_visible' => $createdCategory->is_visible
    ]);

    expect($createdCategory->image)
        ->toEqual(Image::first())
        ->and($createdCategory->image->path)
        ->toEqual($imagePath);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $createdCategory->id,
        'path' => $createdCategory->image->path,
    ]);

    Storage::assertExists($imagePath);
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $category = Category::factory()->invisible()->create([
        'name' => 'testCategory'
    ]);

    post(route('admin.categories.store'), [[
        'name' => $category->name,
        'image' => UploadedFile::fake()->image('testImage'),
        'is_visible' => $category->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 26)], 'name'], // max
    [['name' => 'testCategory'], 'name'], // unique
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
