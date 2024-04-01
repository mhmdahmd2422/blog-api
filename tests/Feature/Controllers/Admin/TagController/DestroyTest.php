<?php

use App\Models\Place;
use App\Models\Tag;
use function Pest\Laravel\{delete};

it('can destroy a tag', function () {
   $tag = Tag::factory()->invisible()->hasPlaces(3)->create();

   delete(route('admin.tags.destroy', $tag))
      ->assertStatus(200)
      ->assertExactJson([
          'message' => __('tags.destroy')
      ]);

   $this->assertDatabaseMissing(Tag::class, [
       'id' => $tag->id,
       'name' => $tag->name,
       'is_visible' => $tag->is_visible
   ]);

   foreach ($tag->places as $place) {
       $this->assertDatabaseMissing(Tag::class, [
           'tag_id' => $tag->id,
           'taggable_type' => Place::class,
           'taggable_id' => $place->id,
       ]);
   }
});
