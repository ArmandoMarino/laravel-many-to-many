<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $technologies = Technology::paginate(10);
        return view('admin.technologies.index', compact('technologies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $technology = new Technology();
        return view('admin.technologies.create', compact('technology'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|unique:types|max:15',
            'color' => 'nullable|string|size:25'
        ], [
            'label.required' => 'Technology field is required',
            'label.max' => 'The type can have maximum of 15 characters',
            'label.unique' => 'This Type name is already taken',
            'color.size' => 'The color must be a hexadecimal code with a pound sign.',
        ]);

        $data = $request->all();

        $technology = new Technology();

        $technology->fill($data);

        $technology->save();

        return to_route('admin.technologies.index', $technology->id)->with('type', 'success')->with('message', "$technology->label created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Technology $technology)
    {
        return to_route('admin.technologies.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technology $technology)
    {
        return view('admin.technologies.edit', compact('technology'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technology $technology)
    {
        $request->validate([
            'label' => ['required', 'string', Rule::unique('technologies')->ignore($technology->id), 'max:15'],
            'color' => 'nullable|string|size:25'
        ], [
            'label.required' => 'Type select is required',
            'label.max' => 'The type can have maximum of 15 characters',
            'label.unique' => 'This Type name is already taken',
            'color.size' => 'The color must be a hexadecimal code with a pound sign.',
        ]);

        $data = $request->all();

        $technology->update($data);

        return to_route('admin.technologies.index', $technology->id)->with('technology', 'success')->with('message', "$technology->label updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technology $technology)
    {
        $technology->delete();

        return to_route('admin.technologies.index')->with('type', 'success')->with('message', "$technology->label deleted successfully");
    }
}
