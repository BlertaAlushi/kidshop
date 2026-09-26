<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Color;
use Inertia\Inertia;

class ColorsController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup,
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = $this->lookup->index();
        return Inertia::render('admin/colors/ColorIndex', ['colors' => $colors]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/colors/ColorCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ColorRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.colors.index')->with('success','created_success');
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
    public function edit(Color $color)
    {
        return Inertia::render('admin/colors/ColorEdit', ['color' => $color]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ColorRequest $request, Color $color)
    {
        $data = $request->validated();
        $this->lookup->update($data,$color);
        return redirect()->route('admin.colors.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
