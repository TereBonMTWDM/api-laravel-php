<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use App\Models\User;
use App\User;

class UserController extends Controller
{
    //
    public function test(Request $request){
        return "Test desde UserController";
    }

    public function testInput(Request $request){

        $dataInput = $request->input('name');
        return "Test Input desde UserController: $dataInput";
    }


    public function register(Request $request){
        //return "Test Registro de Usurios";

        /*/////////1.-RECOGER LOS DATOS DEL USUARIO POR POST:
        //recogemos en un JSON
        //la clave desde postman es "json"
        //se debe dividir cada variable/propiedad del json
        */
        $json = $request->input('json', null); //el null es por si no llega el json; convierte en nulla la variable


        //decodificar json; tomar ese jsonString y convertirlo a objeto de php
        $paramsObj = json_decode($json);
        $paramsArray = json_decode($json, true); //obtiene un array

        //para validar la información, es más eficiente un array

        if(!empty($paramsArray)){
            //var_dump($paramsObj); die();

            //===LIMPIAR LOS DATOS:
            $paramsArray = array_map('trim', $paramsArray);
            //var_dump($paramsArray); die();

            //2.-VALIDAR LOS DATOS DEL USUARIO PROVENIENTES DEL FRONT:
            //laravel ayuda a hacerlo más facil con librería VALIDATOR
            //validación del frontend
            $validate = \Validator::make($paramsArray,  [
                'name' => 'required|alpha',
                'surname' => 'alpha',
                'email' => 'required|email|unique:users',//validando que el campo EMAIL sea el único en la tabla USERS
                'password' => 'required'
            ]);

            //COMPROBAR SI LOS DATOS SON VÁLIDOS
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate-> errors()
                );

                //return response()->json($data, $data['code']);
        
        //        return response()->json($validat-> errors(), 400); 
            }
            else{
                //validacion correcta
                
                //CIFRAR CONTRASEÑA:
                //este sigiuiente cifrado es dinámico, no se requiere así
                //$pwd = password_hash($paramsObj->password, PASSWORD_BCRYPT, ['cost' => 4]);//paramsObj, debe ser el objetojson, no el array
                $pwd = hash('sha256', $paramsObj->password);

                //var_dump("validación correcta"); die();

                //EXISTE USUARIO?
                //laravel ayuda con la propiedad UNIQUE en la validación

                //CREAR USUARIO:
                //por medio de modelo
                $user = new User();
                $user->name = $paramsArray['name'];
                $user->surname = $paramsArray['surname'];
                $user->email = $paramsArray['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //var_dump($user); die();

                //GUARDAR EL USUARIO:
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );                
            }
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos' 
            );
        }

        //convierte array a json
        return response()->json($data, $data['code']);
    }

    public function login(Request $request){


        //var_dump("at login"); die();
        $jwtAuthService = new \JwtAuth(); //llamar al alias del servicio
        //echo $jwtAuthService->signUp(); //llamar al método contenido en el helper

        /*
        //usuario test:
        $email = 'rosario@gmail.com';
        $password = '111';
        $pwd = hash('sha256', $password);
        */

        //1.-Recibir datos por _POST
        $json = $request->input('json', null); //se recibe un json desde el cliente /postman o formulario; 'json' es el parámetro, que puedo o no venir
        //decodificar el json
        $params = json_decode($json);
        $paramsArray = json_decode($json, true); //paramsArray para hacer la validación; decodifica en un array


        //2.- validar los datos que vengan correctos
        $validate = \Validator::make($paramsArray,  [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate-> errors()
            );
        }
        else{

        //3.- cifrar pss
        $pwd = hash('sha256', $params->password);
        
        //4.- devolver token o datos
        $signup = $jwtAuthService->signUp($params->email, $pwd);

        if(!empty($params->getToken)){//si recibe el tercer parámetro, devolver con datos identificados
            $signup = $jwtAuthService->signUp($params->email, $pwd, true);
        }


        }


        //return "Test Login de Usuarios";
        //return $jwtAuthService->signUp($email, $pwd);  //sin 3er parámetro

        //3er parámetro para devolver datos del usuario identificado
        //return response()->json($jwtAuthService->signUp($email, $pwd, true), 200);  //se tiene que devolver STRING no objeto ; se dbe devolver un json
        return response()->json($signup, 200);
    }


    public function update(Request $request){
        //recogeremos el token que llegará en una petición desde la cabecera
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  

        
        //ACTUALIZAR DATOS DEL USUARIO:

        //1.- RECOGER DATOS POR _POST
        $json = $request->input('json', null); //datos que llegan desde la petición del clte

        //codificar ese json para que sea obj de php
        $paramsArray = json_decode($json, true);
        
        //if($checkToken && !empty($json)){
        if($checkToken && !empty($paramsArray)){
            //echo "<h1> login correcto</h1>";



            //sacar usuario idenfiticado, el checkToken
            $user = $jwtAuth->checkToken($token, true);//con el TRUE obtengo la info del usuario identificado del TOKEN: SUB





            //2.- VALIDAR DATOS
             //validación del frontend
             $validate = \Validator::make($paramsArray,  [
                'name' => 'required|alpha', //este alpha no me lo respetó
                'surname' => 'alpha',
                'email' => 'required|email|unique:users,'.$user->sub // esto permite que el email se pueda actualizar, siempre y cuando coincida su idUsuaro:SUB
            ]);



            // 3.- QUITAR LOS CAMPOS QUE "NO" QUIERO ACTUALIZAR
            unset($paramsArray['id']);
            unset($paramsArray['role']);
            unset($paramsArray['password']);
            unset($paramsArray['create_at']);
            unset($paramsArray['remember_token']);//en caso de que llegara en la petición

            //4.- ACTUALIZA USUARIO EN DDBB
            $user_updated = User::where('id', $user->sub)->update($paramsArray);

            //5.- DEVOLVER  ARRAY CON RESULT 
            $data = array(
                'code' => 200,
                'status' => 'success',
                //'message' => $user_updated // devuelve el total de rows afectados
                'message' => $user, //el objeto actualizado o el texto: 'U suario  identificado.'
                'changes' => $paramsArray //envía sólo los datos actualizados
            );

        }
        else{
            //echo "<h1> login INcorrecto</h1>";

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado.'
            );
        }

        return response()->json($data, $data['code']);//para devolver en la respuesat por petición http ; devolverlo en jsonString
    }


    public function upload(Request $request){

        //1.- RECOGER LOS DATOS DE LA PETICIÓN
        $image = $request->file('file0'); //file0 es el nombre del archivo que se subirá, también habrá file1, file2, etc, por el momento


        //VALIDACIÓN DEL ARCHIVO
        //validación del frontend
        $validate = \Validator::make($request->all(),  [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);


        //2.- GUARDAR EL ARCHIVO O IMAGEN
         if(!$image || $validate->fails() ){ //valida también si no hay falla en la imagen
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen: Archivos permitidos únicamente con los siguientes formatos: jpg,jpeg,png,gif'
            );
         }
         else{
  
            $image_name = time().$image->getClientOriginalName(); //getClientOriginalName es el nombre original del archivo que se subió \\ perrito.jpg, 
            //y se le ante-concatena la fecha_actual para que sea un nombre que nunca se repita

              //En Laravel se utiliza una especie de Disco Virtual, o carpeta, y ahí se guardan
            //guardar en el Storage. \Storage para no usar el import, que es un alias del objeto
            //disk('') en qué disco se guardará la imagen; cada disco es una carpeta
            //put() gudarda el archivo
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'Imagen ok.',
                'image' => $image_name
            );
           
         }



      

        //3.- DEVOLVER RESULTADO



        return response()->json($data, $data['code']);
        //devolverlo en texto plano
        //return response()->json($data, $data['code'])->header('Content-Type', 'text/plain'); ya no se usó de esta forma
    }


    public function getImage($filename){
        $exists = \Storage::disk('users')->exists($filename);

        if($exists){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);    
        }
        else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            );
            return response()->json($data, $data['code']);
        }
    }


    public function detail($id){ // para perfil de usaurio
        $user = User::find($id);

        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        }
        else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }
}
