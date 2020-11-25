<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\ProjectTypes;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $projects = Project::with(['customer', 'type'])->mine()->vueTable(Project::$columns);
        $percentageDone = (new Project)->getPercentageDone($projects);

        return response()->json($percentageDone);

    }

    public function search(Request $request)
    {
        $v = $request->validate(['query' => 'required|string']);

        $results = Project::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
       
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();
        return $results;
    }

    public function getProjectsCustomersByIds($customersIds)
    {

        return (new Project)->getProjectsCustomersByIds($customersIds);
    }

    public function create()
    {
        $projectTypes = ProjectTypes::all();
        $project = new Project();
        return [
            'attributes' => $project->attributes(),
            'projectTypes' => $projectTypes
        ];

    }

    public function store(ProjectRequest $request)
    {
        $v = $request->validated();
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $v['type_id'] = $request->request->get('type') ? $request->request->get('type')['id'] : null;
        $project =  new Project();
        Project::create($v);
        return $project;
    }

    public function show(Project $project)
    {

        $project->attributes = $project->attributes();
        $project->type = $project->getType($project->getAttribute('type_id'));
        $project->customer = $project->getCustomer($project->getAttribute('customer_id'));
        $project->contacts =  (new Contact)->getContactByCustomer($project->getAttribute('customer_id'));
        $projectTypes = ProjectTypes::all();
        $project->load($project->attributes->pluck('slug')->toArray());

        $percentageDone = (new Project)->projectPercentageDone($project);

        return [
            'project' => $project,
            'customer' => $project->customer,
            'projectTypes' => $projectTypes,
            'type' => $project->type,
            'percentageDone' => $percentageDone,
            'contacts' => $project->contacts
        ];
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $v = $request->validated();

        $v['type_id'] = $request->request->get('type') ? $request->request->get('type')['id'] : null;
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $project->update($v);
        return $project;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
