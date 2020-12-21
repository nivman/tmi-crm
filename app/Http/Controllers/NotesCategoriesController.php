<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotesCategoriesRequest;
use App\NotesCategories;
use Illuminate\Http\Request;

class NotesCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(NotesCategories::vueTable(NotesCategories::$columns));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return NotesCategories::create($request->request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NotesCategories  $notesCategories
     * @return \Illuminate\Http\Response
     */
    public function show(NotesCategories $notesCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NotesCategories  $notesCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(NotesCategories $notesCategories)
    {

        return response($notesCategories);
    }

    public function update(NotesCategoriesRequest $request, NotesCategories $notesCategories)
    {
        $v = $request->validated();
        $notesCategories->fill($v)->save();
        return $notesCategories;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NotesCategories  $notesCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotesCategories $notesCategories)
    {
        //
    }
}
