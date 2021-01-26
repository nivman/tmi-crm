<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AttributableModel;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;

class UpSale extends ModelForm
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'title', 'amount', 'category.name','project.name'];

    protected $fillable = ['id', 'title', 'amount',  'project_id', 'category_id'];
    protected $with     = ['category', 'project'];

    public function project()
    {

        return $this->belongsTo(Project::class);
    }

    public function category()
    {

        return $this->belongsTo(Category::class );
    }
}
