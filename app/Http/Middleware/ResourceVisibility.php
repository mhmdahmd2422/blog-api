<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceVisibility
{
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

        if ($request->route('comment')) {
            if ($request->comment->is_banned) {
                return response('', 404);
            }
        }

        if ($request->route('tag')) {
            if (! $request->tag->is_visible) {
                return response('', 404);
            }
        }

        if ($request->route('place')) {
            if (! $request->place->is_visible) {
                return response('', 404);
            }
        }

        return $next($request);
    }
}
