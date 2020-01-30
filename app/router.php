<?php
use \Controller\Post\PostController;

Router::get('/index', PostController::class, 'index');
//Router::post('/index', PostController::class, 'index');