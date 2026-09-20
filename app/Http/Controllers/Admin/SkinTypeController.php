<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\SkinType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkinTypeController extends Controller
{

    public function __construct(
        protected LookupInterface $lookup
    ){}
    public function index(){
        $skin_types = $this->lookup->index();
        return Inertia::render('admin/skin_types/SkinTypeIndex', ['skin_types' => $skin_types]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/skin_types/SkinTypeCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FilterRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.skin-types.index')->with('success','created_success');
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
    public function edit(SkinType $skinType)
    {
        $skinType->load('translations:skin_type_id,language_id,name');
        return Inertia::render('admin/skin_types/SkinTypeEdit', ['skin_type' => $skinType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FilterRequest $request, SkinType $skinType)
    {
        $data = $request->validated();
        $this->lookup->update($data, $skinType);
        return redirect()->route('admin.skin-types.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkinType $skinType)
    {
        $skinType->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
