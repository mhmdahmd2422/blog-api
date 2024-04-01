<?php

use App\Models\Place;
use App\Models\Tag;

it('has places', function () {
   $tag = Tag::factory()->hasPlaces(3)->create();

   expect($tag->places)
       ->toHaveCount(3)
       ->each->toBeInstanceOf(Place::class);
});

it('has visible places attribute', function () {
    $visiblePlaces = Place::factory()->visible()->count(2);
    $invisiblePlaces = Place::factory()->invisible()->count(2);
    $tag = Tag::factory()
        ->has($invisiblePlaces)
        ->has($visiblePlaces)
        ->create();

    expect($tag->visiblePlaces->makeHidden('pivot')->toArray())
        ->toEqual(Place::visible()->get()->toArray());
});
