<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
	protected $table = 'catalog';
	protected $primaryKey = 'catno';

	protected $columns = [];

    public function __construct()
    {

    	$values = [
			['catno'  , 'string'        , 'text'    , 'カタログＣＤ'], 
			['shcds'  , 'string'        , 'text'    , 'ｼｮｸﾘｭｰＣＤ'], 
			['eoscd'  , 'string'        , 'text'    , 'ＥＯＳＣＤ'], 
			['mekame' , 'string'        , 'text'    , 'メーカー名'], 
			['shiren' , 'string'        , 'text'    , '仕入先ＣＤ'], 
			['hinmei' , 'string'        , 'text'    , '品名'], 
			['sanchi' , 'string'        , 'text'    , '産地'], 
			['tenyou' , 'string'        , 'text'    , '天・養'], 
			['nouka'  , 'decimal(10,2)' , 'numeric' , '納価'], 
			['baika'  , 'decimal(10,2)' , 'numeric' , '売価'], 
			['stanka' , 'decimal(10,2)' , 'numeric' , '仕入'], 
		];
		$names = [
			'name', 
			'migrate', 
			'hot', 
			'title', 
		];
		foreach ($values as $value)
		{
			$columns[] = (object)array_combine($names, $value);
		}
		$this->columns = collect($columns)->keyBy('name');
		
		$widths = [
			'catno'  => '100px', 
			'shcds'  => '100px', 
			'eoscd'  => '100px', 
			'mekame' => '150px', 
			'shiren' => '100px', 
			'hinmei' => '250px', 
			'sanchi' => '150px', 
			'tenyou' => '100px', 
			'nouka'  => '100px', 
			'baika'  => '100px', 
			'stanka' => '100px', 
		];
		foreach ($this->columns as $name => &$column)
		{
			$column->width = $widths[$name];
		}
    }
    public function getColumns()
    {
    	return $this->columns;
    }
}
