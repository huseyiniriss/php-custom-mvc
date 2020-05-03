<?php
use \Controller\Post\PostController;

Router::get('/index/:slug', PostController::class, 'index');
//Router::post('/index', PostController::class, 'index');
