<?php


namespace App\Helpers;
use Illuminate\Http\Request;

class Filters
{
    public static function filters (Request $request, $entityType) {

        $filter = false;
        $params = [];
        if ($request->query->get('query') == '{}') {
            $request->query->set('query', '');
        }

        if(json_decode($request->query->get('query'))){
            $query = json_decode($request->query->get('query'));
        }

        if($request->query->get('orderBy') === 'priority' || isset($query->priority)) {
            $priorityName = isset($query->priority) ? $query->priority : null;

            $filter = true;
            $params[] = [
                'tableToJoin' => 'task_priorities' ,
                'orderBy' => "{$entityType}.priority_id",
                'orderByValue' => $priorityName ? 'task_priorities.name' : 'task_priorities.id',
                'columnToJoin' => 'task_priorities.id',
                'query' => $priorityName];
        }

        if($request->query->get('orderBy') === 'customer' || isset($query->customer)) {
            $filter = true;
            $customerName = isset($query->customer) ? $query->customer : '';
            $params[] = [
                'tableToJoin' => 'customers' ,
                'orderBy' => "{$entityType}.customer_id",
                'orderByValue' =>  'customers.name',
                'columnToJoin' => 'customers.id',
                'query' => $customerName];

        }

        if($request->query->get('orderBy') === 'project'  || isset($query->project)) {
            $filter = true;
            $projectName = isset($query->project) ? $query->project : '';
            $params[] = [
                'tableToJoin' => 'projects' ,
                'orderBy' => "{$entityType}.project_id",
                'orderByValue' =>  'projects.name',
                'columnToJoin' => 'projects.id',
                'query' => $projectName];

        }
        if($request->query->get('orderBy') === 'status'  || isset($query->status)) {
            $filter = true;
            $statusName = isset($query->status) ? $query->status : '';

            $params[] = [
                'tableToJoin' => 'statuses' ,
                'orderBy' => "{$entityType}.status_id",
                'orderByValue' =>  'statuses.name',
                'columnToJoin' => 'statuses.id',
                'query' => $statusName];
        }

        if($request->query->get('orderBy') === 'category'   || isset($query->category)) {
            $filter = true;
            $categoryName  = isset($query->category) ? $query->category : '';

            $params[] = [
                'tableToJoin' => 'categories' ,
                'orderBy' => "{$entityType}.category_id",
                'orderByValue' =>  'categories.name',
                'columnToJoin' => 'categories.id',
                'query' => $categoryName
            ];
        }

        if($request->query->get('orderBy') === 'type'  || isset($query->type)) {

            $filter = true;
            $typeName = isset($query->type) ? $query->type : '';
            $params[] = [
                'tableToJoin' => 'events_types' ,
                'orderBy' => "events.type_id",
                'orderByValue' =>  'events_types.name',
                'columnToJoin' => 'events_types.id',
                'query' => $typeName];

        }

        if($request->query->get('orderBy') === 'contact' || isset($query->contact)) {
            $filter = true;
            $contactName = isset($query->contact) ? $query->contact : '';
            $params[] = [
                'tableToJoin' => 'contacts' ,
                'orderBy' => "{$entityType}.contact_id",
                'orderByValue' =>  'contacts.first_name',
                'columnToJoin' => 'contacts.id',
                'query' => $contactName];
        }
        return ['params' => $params, 'filter' => $filter] ;
    }

}