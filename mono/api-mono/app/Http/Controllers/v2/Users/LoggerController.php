<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Models\v2\Index\RouteLogger;

class LoggerController extends Controller
{
    public function index()
    {
        $data = RouteLogger::orderBy('created_at', 'desc')->paginate(10);

        return $data;
    }
}
