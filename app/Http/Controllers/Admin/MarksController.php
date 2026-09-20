<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Mark;
use Inertia\Inertia;

class MarksController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup,
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marks = $this->lookup->index();
        return Inertia::render('admin/marks/MarkIndex', ['marks' => $marks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/marks/MarkCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MarkRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.marks.index')->with('success','created_success');
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
    public function edit(Mark $mark)
    {
        return Inertia::render('admin/marks/MarkEdit', ['mark' => $mark]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarkRequest $request, Mark $mark)
    {
        $data = $request->validated();
        $this->lookup->update($data,$mark);
        return redirect()->route('admin.marks.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mark $mark)
    {
        $mark->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
