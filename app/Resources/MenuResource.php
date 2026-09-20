<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'name'=>$this->translation?$this->translation->name:$this->name,
        ];
        return $data;
    }
}
