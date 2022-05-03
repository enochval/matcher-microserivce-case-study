<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    use HasFactory;

    public function setSearchFieldsAttribute($value)
    {
        $this->attributes['search_fields'] = json_encode($value);
    }

    public function getSearchFieldsAttribute()
    {
        return json_decode($this->search_fields, true);
    }

    public function setReturnPotentialAttribute($value)
    {
        $this->attributes['return_potential'] = json_encode($value);
    }

    public function getReturnPotentialAttribute()
    {
        return json_decode($this->return_potential, true);
    }
}
