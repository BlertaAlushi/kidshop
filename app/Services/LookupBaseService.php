<?php

namespace App\Services;
use Illuminate\Support\Str;

class LookupBaseService
{
    protected $model;

    public function index(){
        return $this->model::all();
    }

    public function store($data){
        $slug = $data['slug'] ?? Str::slug($data['name']);
        $item = $this->model::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        foreach ($data['translations'] as $translation) {
            $item->translations()->create([
                'language_id' => $translation['language_id'],
                'name' => $translation['name'],
            ]);
        }
    }

    public function update($data,$item){
        $item->update([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
        ]);

        foreach ($data['translations'] as $translation) {
            $updated = $item->translations()
                ->where('language_id', $translation['language_id'])
                ->update(['name' => $translation['name']]);

            if (!$updated) {
                $item->translations()->create([
                    'language_id' => $translation['language_id'],
                    'name' => $translation['name'],
                ]);
            }
        }
    }
}
