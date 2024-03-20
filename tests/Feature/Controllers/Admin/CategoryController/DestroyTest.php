<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('can delete a category with its image', function () {
    $testImage = UploadedFile::fake()->image('testImage.png');
    $testImagePath = 'uploads/categories/'.$testImage->hashName();
    $category = Category::factory()->has(
        Image::factory()->state(['path' => $testImagePath]), 'image')
        ->create();

    expect($category->image->path)
        ->toEqual($testImagePath);

    delete(route('admin.categories.destroy', $category))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('categories.destroy')
        ]);

    $this->assertDatabaseMissing(Category::class, [
        'id' => $category->id,
        'name' => $category->name
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $category->id,
        'path' => $testImagePath
    ]);

    Storage::assertMissing($testImagePath);
});

it('can delete a category with its single category posts', function () {
    $testImage = UploadedFile::fake()->image('testImage.png');
    $testImagePath = 'uploads/categories/'.$testImage->hashName();
    $category = Category::factory()->has(
        Post::factory()->count(3)->hasComments(3)
    )->has(Image::factory()->state([
        'path' => $testImagePath
    ]), 'image')->create();

    expect($category->image->path)
        ->toEqual($testImagePath)
        ->and($category->posts)
        ->toHaveCount(3);

    delete(route('admin.categories.destroy', $category))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('categories.destroy')
        ]);

    $this->assertDatabaseMissing(Category::class, [
        'id' => $category->id,
        'name' => $category->name
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $category->id,
        'path' => $testImagePath
    ]);

    Storage::assertMissing($testImagePath);

    foreach ($category->posts as $post) {
        $this->assertDatabaseMissing(Post::class, [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'title' => $post->title,
            'description' => $post->description,
        ]);

        foreach ($post->categories as $category) {
            $this->assertDatabaseMissing('category_post', [
                'category_id' => $category->id,
                'post_id' => $post->id
            ]);
        }

        foreach ($post->comments as $comment) {
            $this->assertDatabaseMissing(Comment::class, [
                'id' => $comment->id,
                'post_id' => $post->id,
                'body' => $comment->body,
            ]);
        }
    }
});

it('can delete a category without its multiple category posts', function () {
    $post = Post::factory()->hasComments(3)->hasCategories(3)->create();

    expect($post->categories)
        ->toHaveCount(3)
        ->and($post->comments)
        ->toHaveCount(3);

    $category = $post->categories->first();

    delete(route('admin.categories.destroy', $category))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('categories.destroy')
        ]);

    $this->assertDatabaseMissing(Category::class, [
        'id' => $category->id,
        'name' => $category->name
    ]);

    expect($post->fresh()->categories)
        ->toHaveCount(2)
        ->and($post->comments)
        ->toHaveCount(3);


    $this->assertDatabaseMissing('category_post', [
        'category_id' => $category->id,
        'post_id' => $post->id
    ]);

    foreach ($category->posts as $post) {
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'title' => $post->title,
            'description' => $post->description,
            'is_visible' => $post->is_visible
        ]);

        foreach ($post->comments as $comment) {
            $this->assertDatabaseHas(Comment::class, [
                'id' => $comment->id,
                'post_id' => $post->id,
                'body' => $comment->body,
            ]);
        }
    }
});
