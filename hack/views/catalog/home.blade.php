@extends('layouts/topbar')
@section('title', '商品カタログ')
<?php
$columns = $catalog->getColumns();
$data = $catalog->all();

$todo = [
	['created_at', '150px', '登録時刻', 'text', true], 
	['updated_at', '150px', '修正時刻', 'text', true], 
];
$names = [
	'name', 
	'width', 
	'title', 
	'hot', 
	'readOnly', 
];
foreach ($todo as $values)
{
	$object = (object)array_combine($names, $values);
	$columns[$object->name] = $object;
}
$links_more = array_merge((array)@$links_more, [
	'/vendor/handsontable/dist/handsontable.full.css', 
	'/vendor/handsontable/dist/handsontable.full.js', 
]);
?>
@push('styles')
<style>
#topbar
{
	margin-bottom: 1rem;
}
#content
{
	margin-left: 2rem;
}
#search
{
	width: 30rem;
}
#search > input:first-child
{
	border-radius: 1rem;
	padding: .5rem 1rem;
}
#search > input:first-child + i.icon
{
	right: .5rem;
}
@foreach ($columns as $name => $column)
	#table1 .{{ $name }} { width: {{ $column->width }}; }
@endforeach
#table1 td.created_at, 
#table1 td.updated_at
{
	text-align: center;
}
</style>
@endpush
@section('content')
<p>
	商品カタログ

	<div class="ui icon input" id="search">
		<input type="text" placeholder="商品カタログ検索">
		<i class="search link icon"></i>
	</div>
	<div id="monitor"></div>
</p>
<div id="table1"></div>
@endsection
@push('scripts')
<script>
$(function ()
{
	var search = $('#search > input:first-child');
	var monitor = $('#monitor');
	var button = search.find(' + i.icon');
	search.on('input change', doSearch);
	button.on('click', doSearch);

	var full = getFullData();
	var sel2full = [];//関係表
	var table = handson($('#table1'));
	search.trigger('input');

	function handson(el)
	{
		table = el.handsontable({
			columns: handsonColumns()
			, rowHeaders: true
			, search: true
		});
		return table.handsontable('getInstance');
	}
	function handsonColumns()
	{
		var columns = [];
		@foreach ($columns as $name => $column)
			(function ()
			{
				var column = {};
				column.title = '{{ $column->title }}';
				column.data = '{{ $column->name }}';
				column.type = '{{ $column->hot }}';
				column.className = '{{ $column->name }}';
				column.readOnly = {{ @$column->readOnly ? 'true' : 'false' }};
				columns.push(column);
			})();
		@endforeach
		return columns;
	}
	function getFullData()
	{
		var data = [];
		@foreach ($data as $row)
			(function () {
				var object = {};
				@foreach ($columns as $name => $column)
					object['{{ $name }}'] = '{{ $row->$name }}';
				@endforeach
				data.push(object);
			})();
		@endforeach
		return data;
	}
	function doSearch(e)
	{
		var query = search.val().trim();
		var selected = [];
		sel2full = [];//関係表リセット
		if (query.length === 0)
		{
			selected = full;
		}
		else
		{
			selected = $.grep(full, function (row, i)
			{
				for (col in row)
				{
					var value = row[col];
					if (value && value.indexOf(query) >= 0)
					{
						sel2full.push(i);//関係表エントリ
						return true;
					}
				}
				return false;
			});						
		}
		if (selected.length === 0) selected = [[]];
		table.loadData(selected);
		table.search.query(query);
		table.render();
		
	}
});
</script>
@endpush