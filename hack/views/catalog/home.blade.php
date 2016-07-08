@extends('layouts/topbar')
@section('title', '商品カタログ')
<?php
$columns = $catalog->getColumns();
$values = [
	['created_at', '150px', '登録日時'], 
	['updated_at', '150px', '修正日時'], 
];
$names = [
	'name', 
	'width', 
	'title', 
];
$objects = array_map(function ($values) use ($names) 
{
	return (object)array_combine($names, $values);
}, $values);
$objects = collect($objects)->keyBy('name');
$columns = $columns->merge($objects);
?>
@push('styles')
<style>
#content
{
	margin: 1rem;
}
#search > input:first-child
{
	padding: .5rem 1rem;
	border-radius: 1rem;
	width: 25rem;
}
#search > input:first-child ~ i.remove.icon
{
	right: 2rem;
}
#search > input:first-child ~ i.search.icon
{
	right: .5rem;
}
#table1
{
	width: auto;
}
#table thead th.no
{
	width: 40px;
}
@foreach ($columns as $name => $column)
	#table1 thead th.{{ $name }} { width: {{ $column->width }}; }
@endforeach
#table1 thead th, 
#table1 tbody .no
{
	text-align: center;
}
#table1 tbody .nouka, 
#table1 tbody .baika, 
#table1 tbody .stanka
{
	text-align: right;
}
#table1 tbody .created_at, 
#table1 tbody .updated_at
{
	text-align: center;
}
</style>
@endpush
@section('content')
<p>
	商品カタログ
	<div class="ui icon input search" id="search">
		<input type="text" placeholder="商品カタログを検索">
		<i class="remove link icon"></i>
		<i class="search link icon"></i>
	</div>
</p>
<table class="ui celled fixed table" id="table1">
	<thead>
		<tr>
			<th class="no">No.</th>
			@foreach ($columns as $name => $column)
				<th
					class="{{ $name }}"
					data-name="{{ $column->name }}"
					data-title="{{ $column->title }}"
				>
					{{ $column->title }}
				</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
@endsection
@push('scripts')
<script>
$(function ()
{
	var table = $('#table1');
	var full = getFullData();

	var search = $('#search input:first-child');
	search.find('~ .remove.icon').on('click', resetSearch);
	search.find('~ .search.icon').on('click', doSearch);

	doSearch();
	
	function resetSearch()
	{
		var input = search;
		input.val('').select();
		doSearch();
	}
	function doSearch()
	{
		var input = search;
		var text = input.val().trim();
		var selected = selectData(full, text);
		displayData(table, selected);
	}
	function displayData(table, data)
	{
		var tbody = table.find('tbody').empty();
		var no = 0;
		$.each(data, function (index, row)
		{
			var tr = $('<tr>').appendTo(tbody);
			var td = $('<td>').appendTo(tr).addClass('no').text(++no);
			$.each(row, function (name, value)
			{
				var td = $('<td>').appendTo(tr);
				td.addClass(name);
				td.text(value);
			});
		});
	}
	function selectData(full, text)
	{
		if (text === '') return full;
		var selected = $.grep(full, function (row, index)
		{
			var hit = false;
			$.each(row, function(name, value)
			{
				if (value && value.indexOf(text) >= 0)
				{
					hit = true;
				}
			});
			return hit;
		});
		return selected;
	}

	function getFullData()
	{
		var objects = [];
		@foreach ($catalog->all() as $row)
			(function ()
			{
				var object = {};
				@foreach ($columns as $name => $column)
					<?php $value = $row->$name?>
					object.{{ $name }} = '{{ $value }}';
				@endforeach
				objects.push(object);
			})();
		@endforeach
		return objects;
	}
});
</script>
@endpush