<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Route;

use App\Catalog;

class CatalogController extends Controller
{
    static public function route()
    {
    	$class = '\\' . __class__;
    	Route::get ('/catalog', $class . '@index');
    	Route::post('/catalog/session', $class . '@session');
    	Route::get ('/catalog/validator', $class . '@validator');
    	Route::post('/catalog/check-update', $class . '@checkUpdate');
    }
	
	protected $catalog;
	protected $sessionKey = 'catalog/session';

	public function __construct(Catalog $catalog)
	{
		$this->catalog = $catalog;
	}
    public function showView($view, Request $request)
    {
    	$catalog = $this->catalog;
    	$data = $request->session()->get($this->sessionKey);
    	return view($view, ['catalog' => $catalog, 'data' => $data]);
    }
    public function session(Request $request)
    {
    	$key = $this->sessionKey;
    	$value = $request->input('value');
    	$request->session()->put($key, $value);
    }

    public function index(Request $request)
    {
    	return $this->showView('catalog/home', $request);
    }
    public function validator(Request $request)
    {
    	return $this->showView('catalog/validator', $request);
    }

    public function checkUpdate(Request $request)
    {
    	$data = $request->input('data');
    	$data = json_decode($data);
    	$catno = $data->catno;

    	$catalog = $this->catalog;
    	$find = $catalog->find($catno);
    	$update = $find ? 'update' : 'insert';

    	$data = (object)[];
    	$data->update = $update;
    	return json_encode($data);
    }
}
