<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotesRequest;
use App\Notes;
use App\NotesCategories;
use App\Project;
use Illuminate\Http\Request;

class NotesController extends Controller
{

    public function index(Request $request)
    {
        $projectId = $request->request->get('projectId');
        $categoryId = $request->request->get('categoryId');
        if($categoryId || $projectId) {

            $categoryIds = explode(",", $categoryId);
            $orWhere = $categoryId ? 'where' : 'orWhere';
            $query = Notes::whereIn('note_category_id',$categoryIds)
                ->orderBy('id','DESC')
                ->with('note_category', 'project')
                ->$orWhere(function($query) use ($projectId){
                        if ($projectId) {

                            $query->where('project_id','=',$projectId);
                        }
            })->mine()->vueTable(Notes::$columns);
            return $query;
        }
        return response()->json(Notes::orderBy('id','DESC')->with('note_category', 'project')->mine()->vueTable(Notes::$columns));
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

    public function create()
    {
        $notesCategories = NotesCategories::all();
        $projects = Project::all();

        return ['notesCategories' => $notesCategories , 'projects' => $projects];
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
        $v['project_id'] = $request->request->get('project') ? $request->request->get('project')['id'] : null;
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
       $projects = Project::all();
       return  ['notes' => $notes, 'notesCategories' => $notesCategories, 'projects' => $projects];
    }

    public function update(NotesRequest $request, Notes $notes)
    {
        $v = $request->validated();
        $categoryId = $request->request->get('category_note');
        $projectId = $request->request->get('project');

        if ($categoryId) {
            $v['note_category_id'] = isset($categoryId[0]) ? $categoryId[0]['id'] : $categoryId['id'];
        }else {
            $v['note_category_id'] = null;
        }

        if ($projectId) {
            $v['project_id'] = isset($projectId[0]) ? $projectId[0]['id'] : $projectId['id'];
        }else {
            $v['project_id'] = null;
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

    public function getNoteExtraData()
    {
        $notesCategories = NotesCategories::all();
        $projects = Project::all();

        return ['notesCategories' => $notesCategories , 'projects' => $projects];

    }
}
