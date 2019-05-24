<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class pruebaController extends Controller
{
    //

    public function testOrm(){
        $posts = Post::all();
        /*
        foreach($posts as $p){

            echo "<h1>".$p->title."</h1>";
            echo "<p>".$p->content."</p>";
            echo "<h2 style='color:red;'>{$p->user->name} - Categoría: {$p->category->name}</h2>"; //interpolar
            echo "<hr>"; 
        }
*/
        $categories = Category::all();

        foreach ($categories as $c){
            echo "<h1>".$c->name."</h1>";

            foreach($c->posts as $p){

                echo "<h3>".$p->title."</h3>";
                echo "<p>".$p->content."</p>";
                echo "<h4 style='color:red;'>{$p->user->name} - Categoría: {$p->category->name}</h4>"; //interpolar
                echo "<hr>"; 
            }

        }



        die();
    }


}
