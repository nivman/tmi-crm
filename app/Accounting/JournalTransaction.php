<?php

namespace App\Accounting;

use Illuminate\Database\Eloquent\Model;

class JournalTransaction extends Model
{
    protected $currency = 'USD';
    protected $dates = ['post_date', 'deleted_at', 'udpated_at'];

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($transaction) {
            $transaction->journal->resetCurrentBalances();
        });
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function referencesObject($object)
    {
        $this->subject_type = get_class($object);
        $this->subject_id = $object->id;
        $this->save();
        return $this;
    }

    public function getReferencedObject()
    {
        if ($this->subject_type) {
            $_class = new $this->subject_type;
            return $_class->find($this->subject_id);
        }
        return false;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
