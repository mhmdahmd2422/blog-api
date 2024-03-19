<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceVisibility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('post')) {
            if (! $request->post->is_visible) {
                return response('', 404);
            }
        }

        if ($request->route('category')) {
            if (! $request->category->is_visible) {
                return response('', 404);
            }
        }

        return $next($request);
    }
}
