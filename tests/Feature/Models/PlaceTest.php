<?php

use App\Models\Image;
use App\Models\Place;
use App\Models\Specification;
use App\Models\Tag;

it('has images', function () {
    $place = Place::factory()->hasImages(3)->create();

    expect($place->images)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Image::class);
});

it('has specifications', function () {
    $place = Place::factory()->hasAttached(
        Specification::factory()->count(3), ['description' => 'value']
    )->create();

    expect($place->specifications)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Specification::class);
});

it('has tags', function () {
    $place = Place::factory()->hasTags(3)->create();

    expect($place->tags)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Tag::class);
});

it('has main image attribute', function () {
    $place = Place::factory()->has(
        Image::factory()->is_main()
    )->create();

    expect($place->main_image)
        ->toBeInstanceOf(Image::class)
        ->toEqual($place->images()->isMain()->first());
});

it('has visible tags attribute', function () {
    $visibleTags = Tag::factory()->visible()->count(2);
    $invisibleTags = Tag::factory()->invisible()->count(2);
    $place = Place::factory()
        ->has($invisibleTags)
        ->has($visibleTags)
        ->create();

    expect($place->visibleTags->makeHidden('pivot')->toArray())
        ->toEqual(Tag::visible()->get()->toArray());
});
