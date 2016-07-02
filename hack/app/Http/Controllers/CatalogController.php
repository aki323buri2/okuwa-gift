<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Route;

class CatalogController extends Controller
{
    static public function route()
    {
    	Route::get('/catalog', 'CatalogController@index');
    }
    public function index(Request $request)
    {
    	return view('catalog/home');
    }
}
