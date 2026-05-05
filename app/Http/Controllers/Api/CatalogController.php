<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    private function publicCacheHeaders(int $maxAge = 60, int $swr = 300): array
    {
        return ['Cache-Control' => "public, s-maxage={$maxAge}, stale-while-revalidate={$swr}"];
    }

    public function categories()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json($categories)->withHeaders($this->publicCacheHeaders(300, 600));
    }

    public function products(Request $request)
    {
        $includePrice = (bool) $request->user('sanctum');

        $categorySlug = $request->query('category');
        $featured = $request->query('featured');
        $limit = (int) $request->query('limit', 0);
        if ($limit < 0) {
            $limit = 0;
        }
        if ($limit > 200) {
            $limit = 200;
        }

        $query = Product::query()
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'variants.attributes',
            ])
            ->where('is_active', true)
            ->orderBy('name');

        if (is_string($categorySlug) && $categorySlug !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if (in_array($featured, ['1', 1, true, 'true'], true)) {
            $query->where('is_featured', true);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get();

        $listResponse = response()->json($products->map(fn (Product $p) => $this->productToArray($p, $includePrice)));
        if (! $includePrice) {
            $listResponse->withHeaders($this->publicCacheHeaders(60, 300));
        }

        return $listResponse;
    }

    public function productBySlug(Request $request, string $slug)
    {
        $includePrice = (bool) $request->user('sanctum');

        $product = Product::query()
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'variants.attributes',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $response = response()->json($this->productToArray($product, $includePrice));
        if (! $includePrice) {
            $response->withHeaders($this->publicCacheHeaders(60, 300));
        }

        return $response;
    }

    public function accessoriesByCategory(Request $request, string $categorySlug)
    {
        $includePrice = (bool) $request->user('sanctum');

        $category = Category::query()->where('slug', $categorySlug)->firstOrFail();

        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug',
                'variants.attributes',
            ])
            ->where('is_active', true)
            ->whereHas('compatibilities', fn ($q) => $q->where('category_id', $category->id))
            ->orderBy('name')
            ->get();

        return response()->json($products->map(fn (Product $p) => $this->productToArray($p, $includePrice)));
    }

    private function productToArray(Product $product, bool $includePrice): array
    {
        $imageUrl = $product->image_url;
        if (is_string($imageUrl) && $imageUrl !== '' && ! Str::startsWith($imageUrl, ['http://', 'https://'])) {
            $imageUrl = Storage::disk('public')->url($imageUrl);

            if (is_string($imageUrl) && Str::startsWith($imageUrl, '/')) {
                $imageUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . $imageUrl;
            }
        }

        if (is_string($imageUrl) && $imageUrl !== '' && Str::startsWith($imageUrl, ['http://', 'https://'])) {
            $parsed = parse_url($imageUrl);
            $host = $parsed['host'] ?? null;
            $port = $parsed['port'] ?? null;
            $path = $parsed['path'] ?? '';
            $query = isset($parsed['query']) ? ('?' . $parsed['query']) : '';

            if (in_array($host, ['localhost', '127.0.0.1'], true) && $port === null) {
                $imageUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . $path . $query;
            }
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'image_url' => $imageUrl,
            'created_at' => $product->created_at,
            'is_featured' => $product->is_featured,
            'category' => $product->category,
            'brand' => $product->brand,
            'variants' => $product->variants
                ->where('is_active', true)
                ->values()
                ->map(function ($variant) use ($includePrice) {
                    $arr = [
                        'id' => $variant->id,
                        'title' => $variant->title,
                        'sku' => $variant->sku,
                        'stock_qty' => $variant->stock_qty,
                        'currency' => $variant->currency,
                        'attributes' => $variant->attributes
                            ->map(fn ($a) => [
                                'key' => $a->key,
                                'value' => $a->value,
                                'value_num' => $a->value_num,
                            ])
                            ->values(),
                    ];

                    if ($includePrice) {
                        $arr['price_cents'] = $variant->price_cents;
                    }

                    return $arr;
                }),
        ];
    }
}
