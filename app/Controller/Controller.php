<?php

namespace Controller;

class Controller
{
    public function withJson($data)
    {
        die(json_encode($data));
    }
}