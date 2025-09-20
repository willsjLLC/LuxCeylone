<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestApiController extends Controller
{
     public function textAPI()
    {
        return json_encode([
            'message' => 'This is a Add Citi PRO Api Response',
            'status' => 'success'
        ]);
    }
}
