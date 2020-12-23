<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotesRequest;
use App\Notes;
use App\NotesCategories;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->request->get('categoryId')) {
            $ids = explode(",", $request->request->get('categoryId'));
            return response()->json(Notes::orderBy('id','DESC')->with('note_category')->whereIn('note_category_id', $ids)->mine()->vueTable(Notes::$columns));
        }
        return response()->json(Notes::orderBy('id','DESC')->with('note_category')->mine()->vueTable(Notes::$columns));
    }

    public function changeTitle(Request $request)
    {
        $note = Notes::find($request->query->getIterator()->getArrayCopy()['id']);
        $title = $request->query->getIterator()->getArrayCopy()['input'];
        $note->update(['title' => $title]);
    }

    public function changeSubject(Request $request)
    {
        $note = Notes::find($request->query->getIterator()->getArrayCopy()['id']);
        $title = $request->query->getIterator()->getArrayCopy()['input'];
        $note->update(['subject' => $title]);
    }

    public function changeColor(Request $request)
    {
        $note = Notes::find($request->query->getIterator()->getArrayCopy()['id']);
        $categoryId = $request->query->getIterator()->getArrayCopy()['color-id'];
        $id = $categoryId == -1 ? null : $categoryId;
        $note->update(['note_category_id' => $id]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return NotesCategories::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotesRequest $request)
    {
        $v = $request->validated();
        $v['note_category_id'] = $request->request->get('category_note') ? $request->request->get('category_note')['id'] : null;

        return Notes::create($v);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function show(Notes $notes)
    {
        //
    }


    public function edit(Notes $notes)
    {
       $notesCategories = NotesCategories::all();
        return  ['notes' => $notes, 'notesCategories' => $notesCategories];
    }

    public function update(NotesRequest $request, Notes $notes)
    {
        $v = $request->validated();
        $categoryId = $request->request->get('category_note');

        if ($categoryId) {
            $v['note_category_id'] = isset($categoryId[0]) ? $categoryId[0]['id'] : $categoryId['id'];
        }else {
            $v['note_category_id'] = null;
        }

        $notes->update($v);
        return $notes;
    }

    public function destroy($id)
    {
        $note = Notes::find($id);

        $note->delete();
        return response(['success' => true], 204);
    }

    public function getCategories()
    {

        return NotesCategories::all();
    }
}
