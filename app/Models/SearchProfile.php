<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function setSearchFieldsAttribute($value)
    {
        $this->attributes['search_fields'] = json_encode($value);
    }

    public function getSearchFieldsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setReturnPotentialAttribute($value)
    {
        $this->attributes['return_potential'] = json_encode($value);
    }

    public function getReturnPotentialAttribute($value)
    {
        return json_decode($value, true);
    }
}
