@extends('layouts/topbar')
@section('title', '商品カタログ - 表形式で編集')
<?php
$columns = $catalog->getColumns();
$data = $cache;
$data = json_decode($data);

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
#monitor
{
	width: 15rem;
}
#search > input:first-child
{
	border-radius: 1rem;
	padding: .5rem 1rem;
}
#search > input:first-child + i.icon
{
	right: 2.5rem;
}
#button1
{
	margin-right: 0;
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
	商品カタログを表形式で編集

	<div class="ui form">
		<div class="inline fields">
			<div class="ui icon input field" id="search">
				<input type="text" placeholder="編集対象をさらに検索">
				<i class="remove link icon"></i>
			</div>
			<div class="field" id="monitor">monitor..</div>
			
			<div class="ui buttons field">
				@foreach (range(1, 10) as $no)
					<div class="ui button" id="button{{ $no }}">Button {{ $no }}</div>
				@endforeach
			</div>
		</div>
	</div>
</p>
<div id="table1"></div>
@endsection
@push('scripts')
<script>
$(function ()
{
	var table = $('#table1');
	var search = $('#search > input:first-child');
	var monitor = $('#monitor');
	var showValidator = $('#button1');

	table = handson(table);
	searchInput(table, search, monitor);

	search.trigger('input');

	showValidatorButton(table, showValidator);

});
</script>
@endpush

@push('scripts')
<script>

function handson(el)
{
	var full = getFullData();
	var selected = [];//検索結果
	var sel2full = [];//関係表
	
	table = el.handsontable({
		columns: handsonColumns()
		, rowHeaders: true
		, search: true
	});
	table = table.handsontable('getInstance');

	table.full = full;
	table.selected = selected;
	table.sel2full = sel2full;

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
		@foreach ((array)$data as $row)
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

	return table;
}

</script>
@endpush

@push('scripts')
<script>
function searchInput(table, input, monitor)
{
	table.monitor = monitor;

	var reset = input.find('~ i.remove.icon');
	input.on('input change', doSearch);
	reset.on('click', resetSearch);

	function resetSearch(e)
	{
		e.preventDefault();
		e.stopPropagation();
		input.val('');
		input.select();
		input.trigger('input');
	}
	function doSearch(e)
	{
		var search = $(this);
		var monitor = table.monitor;

		var full = table.full;
		var selected = table.selected;
		var sel2full = table.sel2full;

		var query = search.val().trim();
		selected = [];//検索結果リストのリセット
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

		var text = selected.length + ' / ' + full.length + ' 件を表示';
		monitor.text(text);
	}
}
</script>
@endpush

@push('scripts')
<script>
function showValidatorButton(table, button)
{
	button.text('更新の確認').on('click', showValidator);

	function showValidator(e)
	{
		var props = [];
		var count = table.countCols();
		for (var i = 0; i < count; i++) props.push(table.colToProp(i));
		
		var data = table.getData();
		var objects = [];
		$.each(data, function (index, row)
		{
			var object = {};
			$.each(props, function (index, name)
			{
				object[name] = row[index];
			});
			objects.push(object);
		});

		$.ajax({
			url: '/catalog/session'
			, method: 'post'
			, data: { value: JSON.stringify(objects) }
		})
		.done(function (data)
		{
			location.href = '/catalog/validator';
		})
		;
	}
}
</script>
@endpush