<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $users = (object) [
            ['type' => '1'], 
            ['type' => '2'], 
            ['type' => '3'], 
            ['type' => '4'],
            ['type' => '5'],
            ['type' => '6']
        ];
        return view('home', ['users' => $users]);
    }
}
