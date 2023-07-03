<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class TestController extends Controller
{
    public function index()
    {
        return view('test.index');
    }
}