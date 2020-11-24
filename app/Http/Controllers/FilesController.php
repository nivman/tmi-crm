<?php


namespace App\Http\Controllers;


use App\File;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function deleteFile($id)
    {
        $file = File:: find($id);

        unlink(storage_path("app/public/upload/{$file->customer_id}/{$file->name}"));
        $file->delete();
        return response(['success' => true], 204);
    }

    public function showCustomerFiles($customerId)
    {
       return (new File)->getFilesByCustomerId($customerId);
    }

    public function downloadFile(Request $request)
    {
        $file = File::find($request->query->get('file'));
        $customerId = $request->request->get('customer');
        return response()->download(storage_path("app/public/upload/{$customerId}/{$file->name}"));
    }
}