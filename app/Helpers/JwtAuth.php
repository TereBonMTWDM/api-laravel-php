<?php

    namespace App\Helpers;


    use Firebase\JWT\JWT;
    use Illuminate\Support\Facades\DB; //libería de Laravel a la DB
    use App\User;

    class JwtAuth{

        public $key;

        public function __construct(){
            $this->key = 'clave-super-secreta';
        }
        /*public function signUp(){
            return "Test JWT Auth";
        }*/

        public function signUp($email, $password, $getToken = null){ //$getToken = null es un parametro para devolver el usuar identificado; y es opcional

                
            //EXISTE USUARIO CON SUS CREDENCIALES?
            $user = User::where([
                'email' => $email,
                'password' => $password
            ])->first(); //first obtiene sólo un registro




            //COMPROBARSI ES CORRECTO
            $signup = false;

            if(is_object($user)){
                $signup = true;
            }

            //var_dump("signup: ", $sign)
            



            //GENERAR TOKEON CON LOS DATOS DEL USR IDENTIFICADO
            
            if($signup){
                //datos que irán dentro del tocken:
                $token = array(
                    'sub' => $user->id,
                    'email' => $user->name,
                    'surname' => $user->surname,
                    'iat' => time(),
                    'exp' => time() + (180)
                );


                //librería de jwt p genera el token
                $jwt = JWT::encode($token, $this->key, 'HS256');
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);

                //DEVOLVER LOS DATOS DECODIFICADOS O EL TOKEN
                //si es nulo, que devuelva el token
                //si no, que devuelva la decodificación de ese token
                if(is_null($getToken)){
                    $data = $jwt;
                }
                else{
                    $data = $decoded;
                }

            }
            else{
                $data = array(
                    'status' => 'error',
                    'messsage' => 'Login incorrecto.'
                );
            }
            return $data;
        }


        public function checkToken($jwt, $getIdentity = false){ //jwt a decodificar
            $auth = false; //por default

            try{
                $jwt = str_replace('"', '', $jwt); //eliminar las comillas en caso de que vengan en el json
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            }catch(\UnexpectedValueException $e){
                $auth = false;
            }catch(\DomainException $e){
                $auth = false;
            }

            if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){//si no está vacío, si es un objeto y si existe SUB dentro de sus parámetros
                $auth = true;
            }
            else{
                $auth = false;
            }


            //trabajar con el 3er parámetro:
            if($getIdentity){
                return $decoded;
            }

            return $auth;
        }
    }

 ?>