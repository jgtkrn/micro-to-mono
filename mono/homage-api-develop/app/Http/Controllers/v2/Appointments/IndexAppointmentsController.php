<?php

namespace App\Http\Controllers\v2\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Response\v2\BaseResponse\BaseResponse;

class IndexAppointmentsController extends Controller
{
    private $base_response;

    public function __construct()
    {
        $this->base_response = new BaseResponse;
    }

    public function index()
    {
        return $this->base_response->generate(null, 200, 'This service [Appointments API] run properly.', true);
    }
}
