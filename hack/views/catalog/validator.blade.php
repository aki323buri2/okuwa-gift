@extends('layouts/sidebar')
@section('title', '商品カタログ - 更新の確認')
<?php
$columns = $catalog->getColumns();
$data = json_decode($data);
?>
@push('styles')
<style>
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
</style>
@endpush
@section('content')
<p>
	商品カタログ - 更新の確認
</p>
<p>
	<div class="ui buttons">
		@foreach (range(1, 10) as $no)
			<a href="" class="ui button" id="button{{ $no }}">
				Button {{ $no }}
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
	var reload = $('#button1').text('最新の情報に更新').on('click', checkUpdates);
	checkUpdates();

	function checkUpdates()
	{
		var thead = table.find('thead');
		var tbody = table.find('tbody');
		var rows = tbody.find('tr');

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

		$.ajax({
			url: '/catalog/check-update'
			, method: 'post'
			, data: { _token: '{{ csrf_token() }}', data: JSON.stringify(data) }
		})
		.done(function (data)
		{
			data = JSON.parse(data);
			var update = data.update;
			var title = 
				update === 'insert' ? '新規登録' : (
				update === 'update' ? '修正登録' : 
				update
				);

			td.text(title);
			tr.removeClass('insert update').addClass(update);
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
});
</script>
@endpush