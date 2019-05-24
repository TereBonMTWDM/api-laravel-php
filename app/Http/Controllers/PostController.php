<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Http\Middleware\AuthMiddleware;
use App\Helpers\JwtAuth; //dobleteando el Middleware

class PostController extends Controller
{
    public function __construct(){
        $this->middleware(AuthMiddleware::class, ['except'=> ['index', 'show']]);
    }


    public function test(Request $request){
        return "Test desde PostController";
    }

    public function index(){
        $posts = Post::all()->load('category');//->load('user');

        $data = array(
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        );

        return response()->json($data, $data['code']);
    }

    public function show($id){
        $post = Post::find($id)->load('category');

        if(is_object($post)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $post
            );
        }
        else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe.'
            );
        }
        

        return response()->json($data, $data['code']);
    }

    public function store(Request $request){
        //1.-  RECOGER DATOS POR_POST
        $json = $request->input('json', null);

        $paramsArray = json_decode($json, true);


        if(!empty($paramsArray)){

            //-------------------------------------------------------------------------
            //          dobletenado el token
            //-------------------------------------------------------------------------
            //dobletenado el Middleware: para obtener el ID del usuario identificado
            $token = $request->header('Authorization', null); //null por si no le llega el dato
            $jwtAuth = new \JwtAuth();
            //$checkToken = $jwtAuth->checkToken($token); 
            $user = $jwtAuth->checkToken($token, true);  //true, para devolver el obj decodificado
            



            //2.- VALIDAR LOS DATOS
            $validate = \Validator::make($paramsArray,  [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El post no se ha guardado',
                    'errors' => $validate-> errors()
                );
            }
            else{
                $post = new Post();
                $post->user_id = $user->sub; //revisar token -- se dobletea token
                $post->category_id = $paramsArray['category_id'];
                $post->title = $paramsArray['title'];
                $post->content = $paramsArray['content'];
                $post->image = $paramsArray['image'];

            }

            //3.- GUARDAR EL REGISTRO
            $post->save();

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El post se ha guardado correctamente.',
                'post' => $post
            );                
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha enviado ningún post'
            );
        }
        //4.- DEVOLVER RESULTADO
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
        //1.- RECOGER LOS DATOS POR POST
        $json = $request->input('json', null);

        $paramsArray = json_decode($json, true);

        if(!empty($paramsArray)){
            //2.- VALIDAR DATOS
            $validate = \Validator::make($paramsArray,  [
                'title' => 'required',
                'content' => 'required',
                'user_id' => 'required',
                'category_id' => 'required'
            ]);

            //3.- QUITAR LO QUE NO QUIERO ACTUALIZAR
            unset($paramsArray['id']);
            unset($paramsArray['created_at']);

            //4.- ACTUALIZAR EL REGISTRO
            $category_updated = Category::where('id', $id)->update($paramsArray);


            //5.- DEVOLVER DATOS
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $paramsArray 
            );
        }
        else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ningún post.'
            );
        }

        return response()->json($data, $data['code']);
    }
}
