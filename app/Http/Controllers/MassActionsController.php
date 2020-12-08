<?php


namespace App\Http\Controllers;
use App\Services\MassAction;
use App\Status;
use Illuminate\Http\Request;

class MassActionsController extends Controller
{
    public function action(Request $request)
    {

        (new MassAction())->dispatchActions($request->request->getIterator()->getArrayCopy());
        return response(['success' => true], 204);
    }

    public function getStatues($entity)
    {
        return (new Status)->getAllEntityStatus("App\\$entity");
    }
}