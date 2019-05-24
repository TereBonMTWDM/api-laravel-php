<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;
use App\Http\Middleware\AuthMiddleware;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware(AuthMiddleware::class, ['except'=> ['index', 'show']]);
        //$this->middleware('AuthMiddleware', ['except'=> []]);
    }

    public function test(Request $request){
        return "Test desde CategoryController";
    }

    public function index(){
        $categories = Category::all();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        );

        return response()->json($data, $data['code']);
    }

    public function show($id){
        $category = Category::find($id);

        if(is_object($category)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'categorY' => $category
            );
        }
        else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoría no existe.'
            );
        }
        

        return response()->json($data, $data['code']);
    }

    public function store(Request $request){ //aquí no se valida token?

        //1.-  RECOGER DATOS POR_POST
        $json = $request->input('json', null);

        $paramsArray = json_decode($json, true);

        if(!empty($paramsArray)){

            //2.- VALIDAR LOS DATOS
            $validate = \Validator::make($paramsArray,  [
                'name' => 'required',
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La categoría no se ha creado',
                    'errors' => $validate-> errors()
                );
            }
            else{
                $category = new Category();
                $category->name = $paramsArray['name'];

            }

            //3.- GUARDAR EL REGISTRO
            $category->save();

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'La categoría se ha guardado',
                'category' => $category
            );                
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha enviado ninguna categoría' //'Los datos enviados no son correctos' 
            );
        }
        //4.- DEVOLVER RESULTADO
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
        //la request deberá ser por PUT
        //1.- RECOGER LOS DATOS POR POST
        $json = $request->input('json', null);

        $paramsArray = json_decode($json, true);

        if(!empty($paramsArray)){
            //2.- VALIDAR DATOS
            $validate = \Validator::make($paramsArray,  [
                'name' => 'required'
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
                //'message' => $category, //el objeto actualizado o el texto: 'Usuario  identificado.'
                'category' => $paramsArray 
            );
        }
        else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoría.'
            );
        }

        return response()->json($data, $data['code']);
    }
    

}
