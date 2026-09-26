<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Season;
use App\Models\Size;
use App\Resources\MenuResource;

class FilterOptionsService
{
    public static function menuOptions(){
        return [
            'categories' => MenuResource::collection(Category::where('is_active', true)->select('id','slug','name')->get()),
            'brands' => MenuResource::collection(Brand::where('is_active', true)->select('id','slug','name')->get()),
            'seasons' => MenuResource::collection(Season::where('is_active', true)->select('id','slug','name')->get()),
            'colors' => MenuResource::collection(Color::select('id','name')->get()),
            'sizes' => MenuResource::collection(Size::select('id','name')->orderBy('sort_order')->get()),
        ];
    }

    public static function filters($request){
        return [
            'categories' => $request->categories ?? [],
            'brands' => $request->brands ?? [],
            'seasons' => $request->seasons ?? [],
            'colors' => $request->colors ?? [],
            'sizes' => $request->sizes ?? [],
            'gender' => $request->gender ?? null,
            'per_page'=> $request->per_page ?? null,
            'order_by'=> $request->order_by ?? null,
            'search'=> $request->search ?? null,
        ];
    }
}
