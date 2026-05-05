<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use App\Models\ContentPost;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function pageByPathname(Request $request)
    {
        $pathname = $request->query('pathname');

        if (! is_string($pathname) || $pathname === '') {
            return response()->json(['message' => 'pathname is required'], 422);
        }

        $page = ContentPage::query()
            ->where('pathname', $pathname)
            ->where('is_published', true)
            ->first();

        if (! $page) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($page);
    }

    public function posts(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        if ($limit <= 0) {
            $limit = 50;
        }
        $limit = min($limit, 200);

        $posts = ContentPost::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get([
                'id',
                'slug',
                'title',
                'excerpt',
                'published_at',
                'created_at',
                'updated_at',
            ]);

        return response()->json($posts);
    }

    public function postBySlug(string $slug)
    {
        $post = ContentPost::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($post);
    }
}
