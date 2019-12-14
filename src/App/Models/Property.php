<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PropertyTypes;

class Property extends Model
{

        protected $table = 'properties';

        protected $primaryKey = 'uuid';

        protected $keyType = 'string';

        protected $fillable = [
            'uuid',
            'property_type_id',
            'county',
            'country',
            'postcode',
            'town',
            'description',
            'details_url',
            'address',
            'img_url',
            'thumbnail_url',
            'latitude',
            'longitude',
            'num_bedrooms',
            'num_bathrooms',
            'price',
            'type',
            'created_at',
            'updated_at'
        ];

        public function property_type(){
                return $this->hasOne(PropertyTypes::class, 'id', 'property_type_id');
        }
}