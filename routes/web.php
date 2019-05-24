<?php

use \App\Http\Middleware\AuthMiddleware;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//<ruta> <clase Controller>@<método de la clase>
Route::get('/test-orm', 'pruebaController@testOrm');

//rutas de API TEST
Route::get('/usuario/test', 'UserController@test');//es como la vSSO al html
Route::get('/categoria/test', 'CategoryController@test');
Route::get('/post/test', 'PostController@test');

//rutas de controlladores

/*--------------------------------------------------------------------------
            USUARIOS
--------------------------------------------------------------------------*/
//save usuario
//Route::post('/usuario/registro', 'UserController@register');
Route::post('/api/usuario/testInput', 'UserController@testInput');
Route::post('/api/usuario/register', 'UserController@register');
Route::post('/api/usuario/login', 'UserController@login');
Route::put('/api/usuario/update', 'UserController@update');
//Route::post('/api/usuario/upload', 'UserController@upload');
//Route::post('/api/usuario/upload', ['middleware' => 'authMdw'], 'UserController@upload');
Route::post('/api/usuario/upload', 'UserController@upload')->middleware(AuthMiddleware::class); //con Middleware de AUTENTICACIÓN
//Route::post('/api/usuario/avatar', 'UserController@getImage');
Route::get('/api/usuario/avatar/{filename}', 'UserController@getImage');
Route::get('/api/usuario/detalle/{id}', 'UserController@detail');



/*--------------------------------------------------------------------------
            
            routes: RESOURCES
--------------------------------------------------------------------------*/
Route::resource('/api/categoria', 'CategoryController');
Route::resource('/api/post', 'PostController');
