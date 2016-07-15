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
	width: 30rem;
}
#search > input:first-child ~ i.remove.icon
{
	right: 2.5rem;
}
#search > input:first-child ~ i.search.icon
{
	right: .5rem;
}

#monitor
{
	margin-left: 1rem;
	display: inline-block;
	width: 25rem;
}

#buttons
{
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
	<span id="monitor">monitor..</span>
</p>

<div class="ui secondary menu">
	@foreach (range(1, 10) as $no)
		<a href="#" class="item" id="toolmenu{{ $no }}">
			Tool menu {{ $no }}
		</a>
	@endforeach
</div>

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
	var search = $('#search input:first-child');
	var toggleSelectable = $('#toolmenu1');
	var spreadEditor = $('#toolmenu2');

	searchableTable(table, search);
	selectableTable(table, toggleSelectable);//****************************
	showSpreadEditorButton(table, spreadEditor);
});
</script>
<script>
function searchableTable(table, search)
{
	var full = getFullData();
	search.find('~ .remove.icon').on('click', resetSearch);
	search.find('~ .search.icon').on('click', doSearch);

	doSearch();

	function resetSearch()
	{
		var input = search;
		var empty = input.val().trim() === '';
		input.val('').select();
		if (empty) return;
		doSearch();
	}
	function doSearch()
	{
		var input = search;
		var text = input.val().trim();
		var selected = filterData(full, text);
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
				td.data('name', name);
				td.data('value', value);
				td.text(value);
			});
		});

	}
	function filterData(full, text)
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

	return table;
}
</script>
@endpush
@push('scripts')
@endpush
<script>
function selectableTable(table, toggle)
{
	// elements
	var selectable = table.find('tbody');
	var selall = table.find('thead > tr > th:first-child');
	// class names
	var uiSelectable = 'ui-selectable';
	var uiSelected = 'ui-selected';

	toggle.on('click', toggleSelectable);
	toggleSelectable.apply(toggle);	

	
	function toggleSelectable()
	{
		var me = $(this);
		var cn = 'ui-selectable-off';
		me.toggleClass(cn);
		var off = me.hasClass(cn);
		me.text('行選択を' + (off ? 'オン' : 'オフ'));
		me.prepend($('<i>').addClass(off ? 'hand pointer' : 'hand paper').addClass('icon'));
		if (off)
		{
			destroySelectable(selectable);
		}
		else
		{
			createSelectable(selectable);
		}
	}
	function createSelectable(selectable)
	{
		var tag = 'tr';
		var memo = undefined;

		selectable = selectable.selectable({
			filter: tag
			, start: function (e, ui)
			{
				var sel = $('.' + uiSelected, e.target);
				var all = $(tag, e.target);
				if (sel.length === 0) return;

				var from = all.index(sel.get(0));
				var to   = all.index(sel.get(sel.length - 1));
				memo = [from, to];

			}
			, selecting: function (e, ui)
			{
				var sel = ui.selecting;
				var all = $(sel.tagName, e.target);
				var index = all.index(sel);
				if (e.shiftKey && memo !== undefined)
				{
					var nn = [memo[0], memo[1], index];
					var max = Math.max.apply(null, nn);
					var min = Math.min.apply(null, nn);
					all.slice(min, 1 + max).addClass(uiSelected);
				}

			}
			, selected: function (e, ui)
			{
			}
			, unselected: function (e, ui)
			{
			}
			, stop: function (e, ui)
			{
				var fullSelected = selectable.find('tr:not(.' + uiSelected + ')').length === 0;
				fullSelected ? selall.addClass(uiSelected) : selall.removeClass(uiSelected);
			}
		});
		selall.on('click', function (e)
		{
			var me = $(this).toggleClass(uiSelected);
			var on = me.hasClass(uiSelected);
			selectAllSelectable(on);
		});
		selall.addClass('selall');
		return selectable;
	}
	function selectAllSelectable(on)
	{
		var el = selectable.find('tr');
		on ? el.addClass(uiSelected) : el.removeClass(uiSelected);
	}
	function destroySelectable(selectable)
	{
		if (selectable.hasClass(uiSelectable))
		{
			selectable.selectable('destroy');
			selall.off('click');
			selall.removeClass('selall');

			//選択状態もオフる
			selectAllSelectable(false);
			selall.removeClass(uiSelected);
			
		}
		return selectable;
	}

	return table;
}
</script>
@push('styles')
<style>
/* =================================
 *     for jquery ui selectable
 * ================================= */
#table1 thead th:first-child.selall
{
	cursor: pointer;
}
#table1 .ui-selectable .ui-selectee
{
	background: #eee;
	cursor: pointer;
}
#table1 .ui-selectee.ui-selecting, 
#table1 .ui-selectee.ui-selected, 
#table1 .ui-selecting, 
#table1 .ui-selected
{
	background: #ccc ;
}
</style>
@endpush
@push('scripts')
<script>
function showSpreadEditorButton(table, button)
{
	button.text('表形式で編集');
	button.prepend($('<i>').addClass('table icon'));
	button.on('click', showSpreadEditor);

	function showSpreadEditor()
	{
		var selected = table.find('tbody tr.ui-selected');
		if (selected.length === 0) return;

		var objects = [];
		$.each(selected, function (index, row)
		{
			row = $(row);
			var object = {};
			row.find('> td').each(function ()
			{
				var td = $(this);
				var data = td.data();
				if (data.name === undefined) return;
				var name = data.name;
				var value = data.value;
				object[name] = value;
			});
			objects.push(object);
		});

		$.ajax({
			url: '/catalog/session'
			, method: 'post'
			, data: {value: JSON.stringify(objects)}
		})
		.done(function (data)
		{
			location.href = '/catalog/spread';
		})
		;
	}
}
</script>
@endpush