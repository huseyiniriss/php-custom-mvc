<?php
namespace Controller\Post;

use Controller\Controller;
use Model\User\User;

class PostController extends Controller
{

    public function index($request, $args){
        print_r($args);
        echo '<br>';
        User::select(['B.boardId'])
            ->distinct()
            ->limit(10, 10)
            ->when(1 === 1, function ($q){
                $q->where('a', 1);
            })
            ->orWhere('b',3)
            ->where('b',3)
            ->join('tblsiteconfig C', function ($j){
                $j->on('U.id = C.userId')
                    ->on('U.id = C.userId');
            })
            ->join('tblboardconfig B', function ($j){
                $j->on('U.id = B.userId')
                    ->on('C.id = B.siteId');
            })
            ->getLastQuery();
    }
}
