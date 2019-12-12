<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Property;

class PropertyTypes extends Model
{

    protected $table = 'property_types';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'title',
        'description',
        'created_at',
        'updated_at'
    ];

    public function property(){
        return $this->belongsTo(Property::class);
    }


}