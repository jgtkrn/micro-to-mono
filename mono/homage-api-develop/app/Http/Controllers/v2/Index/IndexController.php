<?php

namespace App\Http\Controllers\v2\Index;

use App\Http\Controllers\Controller;
use App\Http\Response\v2\BaseResponse\BaseResponse;

class IndexController extends Controller
{
    private $base_response;

    public function __construct()
    {
        $this->base_response = new BaseResponse;
    }

    public function index()
    {
        return $this->base_response->generate(null, 200, 'This service [Index API] run properly.', true);
    }
}
