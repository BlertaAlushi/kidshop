<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\SkinConcern;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkinConcernsController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skin_concerns = $this->lookup->index();
        return Inertia::render('admin/skin_concerns/SkinConcernIndex', ['skin_concerns' => $skin_concerns]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/skin_concerns/SkinConcernCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FilterRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.skin-concerns.index')->with('success','created_success');
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
    public function edit(SkinConcern $skinConcern)
    {
        $skinConcern->load('translations:skin_concern_id,language_id,name');
        return Inertia::render('admin/skin_concerns/SkinConcernEdit', ['skin_concern' => $skinConcern]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FilterRequest $request, SkinConcern $skinConcern)
    {
        $data = $request->validated();
        $this->lookup->update($data, $skinConcern);
        return redirect()->route('admin.skin-concerns.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkinConcern $skinConcern)
    {
        $skinConcern->delete();
        return redirect()->back()->with('success','deleted_success');

    }
}
