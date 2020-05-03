<?php
namespace Controller\Post;

use Controller\Controller;
use Model\User;

class PostController extends Controller
{

    public function index($request, $args){
        echo 'Params : ';
        print_r($request);
        echo '<br>';
        echo 'Args : ';
        print_r($args);
        echo '<br>';
        echo 'SQL : ';
        User::select(['U.username'])
            ->where('U.id', 5, '>')
            ->get();

//        $res = User::insert([
//            'nameSurname' => 'deneme2',
//            'email' => 'deneme2',
//            'username' => 'deneme2',
//            'password' => 'deneme2',
//            'phone' => 'deneme2',
//            'userType' => 'deneme2',
//            'apiKey' => 'deneme2',
//            'status' => 'deneme2'
//        ]);

    }
}
