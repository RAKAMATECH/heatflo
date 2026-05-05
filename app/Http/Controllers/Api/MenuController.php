<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function show(string $key)
    {
        $menu = Menu::query()->where('key', $key)->first();
        if (! $menu) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $items = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'menu_id',
                'parent_id',
                'label',
                'url',
                'page_pathname',
                'sort_order',
                'target_blank',
            ]);

        $byParent = [];
        foreach ($items as $item) {
            $pid = $item->parent_id;
            if (! array_key_exists($pid, $byParent)) {
                $byParent[$pid] = [];
            }
            $byParent[$pid][] = $item;
        }

        $build = function ($parentId) use (&$build, $byParent) {
            $children = $byParent[$parentId] ?? [];
            $out = [];

            foreach ($children as $child) {
                $href = null;
                if (is_string($child->page_pathname) && $child->page_pathname !== '') {
                    $href = $child->page_pathname;
                } elseif (is_string($child->url) && $child->url !== '') {
                    $href = $child->url;
                }

                $out[] = [
                    'id' => $child->id,
                    'label' => $child->label,
                    'href' => $href,
                    'target_blank' => (bool) $child->target_blank,
                    'children' => $build($child->id),
                ];
            }

            return $out;
        };

        return response()->json([
            'key' => $menu->key,
            'name' => $menu->name,
            'items' => $build(null),
        ]);
    }
}
