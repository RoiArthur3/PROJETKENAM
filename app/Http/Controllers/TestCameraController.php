<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestCameraController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Test Camera Controller fonctionne!',
            'time' => now(),
            'controller' => 'TestCameraController',
        ]);
    }
}
