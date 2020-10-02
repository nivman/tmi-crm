<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $casts    = ['extra' => 'array'];
    protected $fillable = [
        'name', 'address', 'email', 'phone', 'footer', 'logo', 'extra', 'state', 'country',
        'state_name', 'country_name', 'template', 'show_image', 'show_tax', 'show_discount',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function getCountryNameAttribute()
    {
        return (get_country($this->country))->name;
    }

    public function getStateNameAttribute()
    {
        $cs = getCS($this->country, $this->state);
        return $cs['state']->name;
    }

    public function setExtraAttribute($value)
    {
        $this->attributes['extra'] = trim($value, '"');
    }
}
