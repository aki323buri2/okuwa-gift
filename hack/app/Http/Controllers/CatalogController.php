<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Route;

use App\Catalog;

class CatalogController extends Controller
{
	protected $catalog;
	public function __construct(Catalog $catalog)
	{
		$this->catalog = $catalog;
	}
    static public function route()
    {
    	Route::get('/catalog', 'CatalogController@index');
    }
    public function index(Request $request)
    {
    	return view('catalog/home');
    }
}
