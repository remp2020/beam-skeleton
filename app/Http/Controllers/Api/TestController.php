<?php

namespace App\Http\Controllers\Api;

class TestController
{
    public function create()
    {
        return response()->json([
            'message' => 'test message',
            'code' => 'test_code',
        ]);
    }
}