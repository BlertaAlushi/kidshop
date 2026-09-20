<?php

namespace App\Services;

use App\Models\BodyPart;
use App\Models\Extra;
use App\Models\Mark;
use App\Models\ProductType;
use App\Models\SkinConcern;
use App\Models\SkinType;
use App\Resources\MenuResource;
use Illuminate\Http\Request;

class FilterOptionsService
{
    public static function menuOptions(){
        return [
            'bodyParts' => MenuResource::collection(BodyPart::select('id','slug','name')->with('translation:body_part_id,name')->get()),
            'skinTypes' => MenuResource::collection(SkinType::select('id','slug','name')->with('translation:skin_type_id,name')->get()),
            'skinConcerns' => MenuResource::collection(SkinConcern::select('id','slug','name')->with('translation:skin_concern_id,name')->get()),
            'productTypes' => MenuResource::collection(ProductType::select('id','slug','name')->with('translation:product_type_id,name')->get()),
            'extras' => MenuResource::collection(Extra::select('id','slug','name')->with('translation:extra_id,name')->get()),
            'marks' => MenuResource::collection(Mark::select('id','slug','name')->get()),
        ];
    }

    public static function filters($request){
        return [
            'skin_types' => $request->skin_types ?? [],
            'skin_concerns' => $request->skin_concerns ?? [],
            'product_types' => $request->product_types ?? [],
            'extras' => $request->extras ?? [],
            'per_page'=> $request->per_page ?? null,
            'order_by'=> $request->order_by ?? null,
            'search'=> $request->search ?? null,
        ];
    }
}
