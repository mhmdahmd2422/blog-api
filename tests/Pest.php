<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(
    Tests\TestCase::class,
    LazilyRefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function loginAsUser(?User $user = null): User
{
    $user = $user ?? User::factory()->create();
    \Laravel\Passport\Passport::actingAs($user);

    return $user;
}

function responseData($resource)
{
    $resource = $resource->response()->getData(true);

    return $resource['data'];
}

function responsePaginatedData($resource)
{
    $resource = $resource->response()->getData(true);

    return $filteredResource = [
        'current_page' => $resource['meta']['current_page'],
        'data' => $resource['data'],
        'first_page_url' => $resource['links']['first'],
        'from' => $resource['meta']['from'],
        'last_page' => $resource['meta']['last_page'],
        'last_page_url' => $resource['links']['last'],
        'links' => $resource['meta']['links'],
        'next_page_url' => $resource['links']['next'],
        'path' => $resource['meta']['path'],
        'per_page' => $resource['meta']['per_page'],
        'prev_page_url' => $resource['links']['prev'],
        'to' => $resource['meta']['to'],
        'total' => $resource['meta']['total'],
    ];
}
