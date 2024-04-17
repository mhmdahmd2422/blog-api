<?php

use App\Http\Resources\Admin\SpecificationResource;
use App\Models\Specification;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all specifications', function () {
   $specifications = Specification::factory()->count(10)->hasImage()->create();

   get(route('admin.specifications.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'specifications' => responseData(
               SpecificationResource::collection($specifications->load('image')
                   ->paginate(pagination_length('specification')))
           )
       ]);
});
