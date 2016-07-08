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
        Route::post('/catalog/do-update', $class . '@doUpdate');
    	Route::get ('/catalog/search', $class . '@search');
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
    	$cache = $request->session()->get($this->sessionKey);

    	return view($view, ['catalog' => $catalog, 'cache' => $cache]);
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



    	$result = (object)[];
    	$result->update = $update;
    	return json_encode($result);
    }
    public function doUpdate(Request $request)
    {
    	$update = $request->input('update');
    	$data = $request->input('data');
    	$data = json_decode($data);

    	$catalog = $this->catalog;
    	
    	if ($update === 'update')
    	{
    		$catno = $data->catno;
    		$catalog = $catalog->find($catno);
    	}

    	foreach ($data as $name => $value)
    	{
    		$catalog->$name = $value;
    	}

    	$result = (object)[];

        $result->update = $update;

    	try
    	{
    		$result->save = $catalog->save();
    	}
    	catch (\Exception $e)
    	{
    		$result->error = $e->getMessage();
    	}

    	return json_encode($result); 
    }

    public function search(Request $request)
    {
        $catalog = $this->catalog;
        $search = $request->input('search');

        return $search;
    }
}
