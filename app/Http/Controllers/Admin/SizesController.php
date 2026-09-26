<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SizeRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Size;
use Inertia\Inertia;

class SizesController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup,
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = $this->lookup->index();
        return Inertia::render('admin/sizes/SizeIndex', ['sizes' => $sizes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/sizes/SizeCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SizeRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.sizes.index')->with('success','created_success');
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
    public function edit(Size $size)
    {
        return Inertia::render('admin/sizes/SizeEdit', ['size' => $size]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SizeRequest $request, Size $size)
    {
        $data = $request->validated();
        $this->lookup->update($data,$size);
        return redirect()->route('admin.sizes.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $size->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
