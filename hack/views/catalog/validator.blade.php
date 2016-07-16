@extends('layouts/topbar')
@section('title', '商品カタログ - 更新の確認')
<?php
$columns = $catalog->getColumns();
$data = $cache;
$data = json_decode($data);
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
#table1
{
	/*width: auto;*/
}
#table1 th.no
{
	width: 30px;
}
@foreach ($columns as $name => $column)
	#table1 th.{{ $name }} { width: {{ $column->width }}; }
@endforeach
#table1 th
{
	text-align: center;
}
#table1 tbody td.no, 
#table1 tbody td.nouka, 
#table1 tbody td.baika, 
#table1 tbody td.stanka
{
	text-align: right;
} 
#table1 th, 
#table1 td.no
{
	background: #ecf0f1; 

}

#table1 tbody tr.insert td:not(.no):not(.status)
{
	color: #fff;
	background: #e74c3c;
}
#table1 tbody tr.update td:not(.no):not(.status)
{
	/*color: #fff;*/
	/*background: #2980b9;*/
	background: rgba( 41,128,185,.3);
}
#table1 tbody tr.update td:not(.no):not(.status).dirty
{
	background: #f1c40f
}

</style>
@endpush
@section('content')
<p>
	商品カタログ - 更新の確認
</p>
<p>
	<div class="ui secondary menu">
		@foreach (range(1, 10) as $no)
			<a href="#" class="ui item" id="smenu{{ $no }}">
				smenu {{ $no }}
			</a>
		@endforeach
	</div>
</p>
<table class="ui celled table" id="table1">
	<thead>
		<tr>
			<th class="no">
				No.
			</th>
			@foreach ($columns as $name => $column)
				<th
					class="{{ $name }}"
					data-name="{{ $column->name }}"
					data-title="{{ $column->title }}"
				>
					{{ $column->title }}
				</th>
			@endforeach

			<th class="status">
				更新の確認
			</th>
		</tr>
	</thead>
	<tbody>
		<?php $no = 0 ?>
		@foreach ((array)@$data as $row)
			<tr>
				<td class="no">
					{{ ++$no }}
				</td>

				@foreach ($columns as $name => $column)
					<?php $value = $row->$name ?>
					<td
						class="{{ $name }}"
						data-name="{{ $column->name }}"
						data-value="{{ $value }}"
					>
						{{ $value }}
					</td>
				@endforeach

				<td class="status">
					
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endsection
@push('scripts')
<script>
$(function ()
{
	var table = $('#table1');
	var back = $('#smenu1');
	var reload = $('#smenu2');
	var save = $('#smenu3');

	backButton(table, back);
	checkUpdateButton(table, reload);
	saveButton(table, save);
	updatableTable(table);

	reload.trigger('click');

	selectRowBehavior(table);
});
</script>
@endpush
@push('scripts')
<script>	
// ===========================================================
function checkUpdateButton(table, button)
{
	button.text('最新の情報に更新').on('click', checkUpdates);
	button.prepend($('<i>').addClass('refresh icon'));

	table.getDataFromRow = getDataFromRow;

	function checkUpdates()
	{
		var thead = table.find('thead');
		var tbody = table.find('tbody');
		var rows = tbody.find('tr');

		rows.addClass('checking');

		rows.each(function ()
		{
			var tr = $(this);
			checkUpdate(tr);
		});
	}
	function checkUpdate(tr)
	{
		var td = tr.find('.status');
		var data = getDataFromRow(tr);

		table.saveStatus('checking');

		$.ajax({
			url: '/catalog/check-update'
			, method: 'post'
			, data: {
				data: JSON.stringify(data)
			}
		})
		.done(function (data)
		{
			data = JSON.parse(data);
			var update = data.update;
			var dirty = JSON.parse(data.dirty);
			
			var title = 
				update === 'insert' ? '新規登録' : (
				update === 'update' ? '修正登録' : (
				update === 'nochange' ? '変更なし' : 
				update
				));

			td.text(title);
			tr.removeClass('insert update').addClass(update);
			tr.data('update', update);

			tr.find('td').removeClass('dirty');
			$.each(dirty, function (name, value)
			{
				tr.find('td.' + name).addClass('dirty');
			});

			tr.removeClass('checking');
			if (tr.parent().find('.checking').length === 0)
			{
				table.saveStatus('ready');
			}
		});
	}
	function getDataFromRow(tr)
	{
		var data = {};
		tr.find('> td[data-name]').each(function ()
		{
			var td = $(this);
			var name = td.data('name');
			var value = td.data('value');
			data[name] = value;
		});
		return data;
	}
}
</script>
@endpush
@push('scripts')
<script>
// ===========================================================
function backButton(table, button)
{
	button.text('表形式編集へ戻る').on('click', backToSpread);
	button.prepend($('<i>').addClass('backward icon'));
	function backToSpread()
	{
		var objects = [];

		@foreach ((array)$data as $row)
			(function ()
			{
				var object = {};
				@foreach ($row as $name => $value)
					object.{{ $name }} = '{{ $value }}';
				@endforeach
				objects.push(object);
			})();
		@endforeach
		
		$.ajax({
			url: '/catalog/session'
			, method: 'post'
			, data: { value: JSON.stringify(objects) }
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
@push('scripts')
<script>
// ===========================================================
function saveButton(table, button)
{
	table.saveStatus = function (status)
	{
		switch (status)
		{
		case 'checking': 
			button.empty().text('更新を確認しています').off('click');
			button.prepend($('<i>').addClass('fa fa-spinner fa-spin fa-puls'));
			break;
		case 'ready': 
			button.empty().text('データを更新する').on('click', table.doUpdates);
			button.prepend($('<i>').addClass('database icon'));
			break;
		}
	}
}
</script>
@endpush
@push('scripts')
<script>
// ===========================================================
function updatableTable(table)
{
	table.doUpdates = doUpdates;

	function doUpdates()
	{
		var rows = table.find('> tbody').find('.insert, .update');

		rows.addClass('updating');
		rows.each(function ()
		{
			var tr = $(this);
			doUpdate(tr, onUpdatesFinished);
		});
	}
	function doUpdate(tr, onUpdatesFinished)
	{
		var td = tr.find('.status');
		var update = tr.data('update');
		var data = table.getDataFromRow(tr);

		updateStatus(td, 'updating');

		$.ajax({
			url: '/catalog/do-update'
			, method: 'post'
			, data: {
				update: update
				, data: JSON.stringify(data)
			}
		})
		.done(function (data)
		{
			updateStatus(td, 'done');
		})
		.fail(function (xhr, error, thrown)
		{
			updateStatus(td, 'error');
		})
		.always(function ()
		{
			tr.removeClass('updating');
			if (tr.parent().find('.updating').length === 0)
			{
				onUpdatesFinished();
			}
		})
		;
	}
	function updateStatus(td, status)
	{
		var tr = td.closest('tr');

		tr.addClass(status);
		
		var update = tr.data('update');
		var text = 
			update === 'insert' ? '追加' : (
			update === 'update' ? '更新' : (
			update
			));

		switch (status)
		{
		case 'updating': 
			td.empty().text(text + 'しています・・・');
			td.prepend($('<i>').addClass('fa fa-spinner fa-spin fa-puls'));
			break;
		case 'done': 
			td.empty().text(text + 'が正常に終了しました');
			tr.removeClass(update);
			break;
		case 'error':
			td.empty().text(text + 'が異常終了しました！！！');
			break;
		}
	}
	function onUpdatesFinished()
	{
	}
}
</script>
@endpush
@push('scripts')
<script>
function selectRowBehavior(table)
{
	

}
</script>
@endpush