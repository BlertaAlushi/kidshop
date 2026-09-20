<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Language;
use Illuminate\Support\Str;

class LanguageService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Language::class;
    }

    public function store($data){
        $this->model::create([
            'code' => $data['code'],
            'language' => $data['language']
        ]);
    }

    public function update($data,$item){
        $item->update([
            'code' => $data['code'],
            'language' => $data['language']
        ]);
    }

    public static function getCurrentLanguageId()
    {
        $locale = app()->getLocale();
        return Language::where('code', $locale)->value('id');
    }
}
