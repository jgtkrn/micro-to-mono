<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Response\v2\BaseResponse\BaseResponse;

class IndexEldersController extends Controller
{
    private $base_response;

    public function __construct()
    {
        $this->base_response = new BaseResponse;
    }

    public function index()
    {
        return $this->base_response->generate(null, 200, 'This service [Elders API] run properly.', true);
    }
}
