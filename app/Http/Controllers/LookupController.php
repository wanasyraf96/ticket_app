<?php

namespace App\Http\Controllers;

use App\Models\Lookup;
use App\Traits\LookupTrait;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    use LookupTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lookup = Lookup::select('for')->get();
        $lookup = array_map(function ($element) {
            $element['data'] = $this->lookupElement($element['for']);
            return $element;
        }, $lookup->toArray());
        return $lookup;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Lookup $lookup)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lookup $lookup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lookup $lookup)
    {
        //
    }
}
