<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //tabla
    protected $table = 'categories';

    //indicar una relación: de uno a muchos
    public function posts(){
        //con esto relaciona: al llamar al método, obtener todos los objetos de tipo POST relacionados con la categoría 
        return $this->hasMany('app\Post'); //hasMany es de uno a muchos 
    }



}
