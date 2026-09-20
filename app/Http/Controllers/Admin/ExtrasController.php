<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Extra;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExtrasController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup
    ){}
    public function index(){
        $extras = $this->lookup->index();
        return Inertia::render('admin/extras/ExtraIndex', ['extras' => $extras]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/extras/ExtraCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FilterRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.extras.index')->with('success','created_success');
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
    public function edit(Extra $extra)
    {
        $extra->load('translations:extra_id,language_id,name');
        return Inertia::render('admin/extras/ExtraEdit', ['extra' => $extra]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FilterRequest $request, Extra $extra)
    {
        $data = $request->validated();
        $this->lookup->update($data, $extra);
        return redirect()->route('admin.extras.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Extra $extra)
    {
        $extra->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
