<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup
    ){}
    public function index(){
        $languages = $this->lookup->index();
        return Inertia::render('admin/languages/LanguageIndex', ['languages' => $languages]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/languages/LanguageCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return Redirect::route('admin.languages.index')->with('success','created_success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return Inertia::render('admin/languages/LanguageEdit', ['language' => $language]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, Language $language)
    {
        $data = $request->validated();
        $this->lookup->update($data,$language);
        return Redirect::route('admin.languages.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
