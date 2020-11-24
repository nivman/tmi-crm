<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    public static $columns = ['id', 'created_at',  'name', 'customer_id'];

    protected $fillable = ['id', 'created_at',  'name', 'customer_id', 'event_id'];

    public function getFilesByCustomerId($customerId)
    {
        return DB::table('files')->select('id', 'name','created_at')
            ->where('customer_id', $customerId)
            ->get()->toArray();
    }

    public function getFileByEventId($eventId)
    {
        return DB::table('files')->select('id', 'name','created_at')
            ->where('event_id', $eventId)
            ->get()->toArray();
    }


    /**
     * @param Request $request
     * @param Event $event
     */
    public function moveFiles(Request $request, Event $event): void
    {
        $path = storage_path("app/public/upload/" . $request->contact['customer_id']);
        $file = $this->getFileByEventId($event->id);
        $MAIN_SYSTEM_CUSTOMER = '36';
        $oldPath = "public/upload/{$MAIN_SYSTEM_CUSTOMER}/" . $file[0]->name;

        if (!is_dir($path)) {
            Storage::makeDirectory("public/upload/" . $request->contact['customer_id'], 0775, true);

        }
        $newPath = "public/upload/" . $request->contact['customer_id'] . '/' . $file[0]->name;

        Storage::disk('local')->move($oldPath, $newPath);
        $newCustomerId = ['customer_id' => $request->contact['customer_id']];
        $updateFile = File::find($file[0]->id);
        $updateFile->update($newCustomerId);
    }

    public function saveFile(Request $request)
    {
        $uploadFile = $request->file;
        $name = $request->file->getClientOriginalName();

        $path = storage_path("app/public/upload/");

        if(!is_dir($path)){
            Storage::makeDirectory("public/upload/", 0775, true);
        }
        $uploadFile->storeAs('public/upload/'.$request->customerId, $name);
        $fileData = ['name'=> $name, 'customer_id' => $request->customerId];
        File::create($fileData);
    }
}