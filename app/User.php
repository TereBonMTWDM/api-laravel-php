<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'updateAt',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    //indicar una relación: de uno a muchos
    //un usuario puede tener muchos posts 
    public function posts(){
        //con esto relaciona: al llamar al método, obtener todos los objetos de tipo POST relacionados con el usuario, todos los qe él haya creado 
        return $this->hasMany('app\Post'); //hasMany es de uno a muchos 
    }
}
