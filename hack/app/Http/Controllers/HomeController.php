<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use \Route;

class HomeController extends Controller
{
    static public function route()
    {
    	Route::get('/', 'HomeController@home');
    	Route::get('/home', 'HomeController@home');
    }
    public function home(Request $request)
    {
    	return view('home');
    }
}
