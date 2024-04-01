<?php

use App\Models\Image;
use App\Models\Place;
use App\Models\Specification;

it('has an image', function () {
    $specification = Specification::factory()->hasImage()->create();

    expect($specification->image)
        ->toBeInstanceOf(Image::Class);
});

it('has places', function () {
    $specification = Specification::factory()->hasAttached(
        Place::factory()->visible()->count(3), ['description' => 'value']
    )->create();

    expect($specification->places)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Place::class);
});

it('has icon attribute', function () {
    $specification = Specification::factory()->has(
        Image::factory()->is_main()
    )->create();

    expect($specification->icon)
        ->toBeInstanceOf(Image::class)
        ->toEqual($specification->image()->isMain()->first());
});

it('has visible places attribute', function () {
    $visiblePlaces = Place::factory()->visible()->count(2);
    $invisiblePlaces = Place::factory()->invisible()->count(2);
    $specification = Specification::factory()
        ->hasAttached($invisiblePlaces, ['description' => 'value'])
        ->hasAttached($visiblePlaces, ['description' => 'value'])
        ->create();

    expect($specification->visiblePlaces->makeHidden('pivot')->toArray())
        ->toEqual(Place::visible()->get()->toArray());
});
