<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * Ambil semua banner aktif, urut berdasarkan sort_order.
     */
    public function index(): JsonResponse
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($b) => [
                'id'          => $b->id,
                'title'       => $b->title,
                'subtitle'    => $b->subtitle,
                'description' => $b->description,
                'image_url'   => $b->image ? url('storage/' . $b->image) : null,
                'color'       => $b->color,
            ]);

        return response()->json(['data' => $banners]);
    }
}
