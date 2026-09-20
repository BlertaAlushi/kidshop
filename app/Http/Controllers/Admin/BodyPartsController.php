<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\BodyPart;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BodyPartsController extends Controller
{
   public function __construct(
     protected LookupInterface $lookup
   ){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $body_parts = $this->lookup->index();
        return Inertia::render('admin/body_parts/BodyPartIndex', ['body_parts' => $body_parts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/body_parts/BodyPartCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FilterRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.body-parts.index')->with('success','created_success');
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
    public function edit(BodyPart $bodyPart)
    {
        $bodyPart->load('translations:body_part_id,language_id,name');
        return Inertia::render('admin/body_parts/BodyPartEdit', ['body_part' => $bodyPart]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FilterRequest $request, BodyPart $bodyPart)
    {
        $data = $request->validated();
        $this->lookup->update($data, $bodyPart);
        return redirect()->route('admin.body-parts.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BodyPart $bodyPart)
    {
        $bodyPart->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
