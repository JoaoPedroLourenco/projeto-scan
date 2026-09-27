<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    function teste() {
        return response()->json(["ok" => true]);
    }
}
