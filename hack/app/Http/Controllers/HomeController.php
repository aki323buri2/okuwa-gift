<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use \Route;

class HomeController extends Controller
{
    static public function route()
    {
    	$class = '\\' . __class__;
    	Route::get('/', $class . '@home');
    	Route::get('/home', $class . '@home');
    }
    public function home(Request $request)
    {
    	return view('home');
    }
}
