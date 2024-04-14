<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Http\Response\v2\BaseResponse\BaseResponse;

class IndexUserController extends Controller
{
    private $base_response;

    public function __construct()
    {
        $this->base_response = new BaseResponse;
    }

    public function index()
    {
        return $this->base_response->generate(null, 200, 'This service [User API] run properly.', true);
    }
}
