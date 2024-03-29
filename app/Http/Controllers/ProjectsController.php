<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\ProjectTypes;
use App\Task;
use App\UpSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $projects = Project::with(['customer', 'type','upSale'])->orderBy('active','DESC')->mine()->vueTable(Project::$columns);
        $totalProjectsPrice = (new Project)->setProjectTotalPrice($projects);
        $percentageDone = (new Project)->getPercentageDone($totalProjectsPrice);

        return response()->json($percentageDone);

    }

    public function list()
    {
        return Project::all();
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

    public function getCustomerProjectsList($id)
    {
        $projects = Project::with(['customer', 'type'])->where(['customer_id' => $id])->mine()->vueTable(Project::$columns);
        $percentageDone = (new Project)->getPercentageDone($projects);

        return response()->json($percentageDone);
    }

    public function getProjectsFormCustomersByIds($customersIds)
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

    public function tableFilter()
    {
        return DB::table('projects')->select('id', 'name as text')
            ->get();
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
        }catch (\Exception $e) {

            return response()->json(['data'=> $e->getMessage()], '203');

        }



        return response(['success' => true], 204);
    }

    public function getProjectById($projectId)
    {
        $project = Project::find($projectId);
        return ['id' => $project->id, 'name' => $project->name];

    }
}
