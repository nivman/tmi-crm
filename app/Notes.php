<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notes extends Model
{
    use  LogActivity, Restrictable, VueTable;

    public static $columns = [ 'title', 'subject'];

    protected $fillable = ['title', 'subject', 'created_at', 'note_category_id', 'project_id'];


    public function note_category()
    {
        return $this->belongsTo(NotesCategories::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
