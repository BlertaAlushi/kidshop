<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Country;
use Inertia\Inertia;

class CountriesController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup,
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = $this->lookup->index();
        return Inertia::render('admin/countries/CountryIndex', ['countries' => $countries]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/countries/CountryCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.countries.index')->with('success','created_success');
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
    public function edit(Country $country)
    {
        return Inertia::render('admin/countries/CountryEdit', ['country' => $country]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country)
    {
        $data = $request->validated();
        $this->lookup->update($data,$country);
        return redirect()->route('admin.countries.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->back()->with('success','deleted_success');
    }
}
