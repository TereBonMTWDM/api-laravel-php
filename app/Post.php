<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //table
    protected $table = 'posts';

    //relación muchos a uno
    //muchos post pueden ser creados por un usuario
    //o pertenecer a una misma categoría
    //obtiene el objeto relacionado con USER
    public function user(){
        return $this->belongsTo('App\User', 'user_id'); //de la tabla USER
    }

    public function category(){
        return $this->belongsTo('App\Category', 'category_id'); //de la tabla USER
    }

}
