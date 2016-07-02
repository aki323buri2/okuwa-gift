@extends('layouts/sidebar')
@section('title', '商品カタログ')
<?php
$links_more = array_merge((array)@$links_more, [
	'/vendor/handsontable/dist/handsontable.full.css', 
	'/vendor/handsontable/dist/handsontable.full.js', 
]);
$columns = $catalog->getColumns();
?>
@push('styles')
<style>
@foreach ($columns as $name => $column)
	#handson .{{ $name }} { width: {{ $column->width }}; }
@endforeach
</style>
@endpush
@section('content')
<p>
	商品カタログ
</p>
<div id="handson"></div>
@endsection
@push('scripts')
<script>
$(function ()
{
	var hot = handson($('#handson'));

	function handson(el)
	{
		var hot = el.handsontable({
			columns: handsonColumns()
			, data: handsonData()
			, rowHeaders: true
			, afterChange: handsonAfterChange
		});
		return hot.handsontable('getInstance');
	}


	function handsonColumns()
	{
		var columns = [];
		@foreach ($columns as $name => $column)
			(function ()
			{
				var object = {};
				object.data = '{{ $name }}';
				object.title = '{{ $column->title }}';
				object.type = '{{ $column->hot }}';
				object.className = '{{ $column->name }}';
				columns.push(object);
			})();
		@endforeach 
		return columns;
	}
	function handsonData()
	{
		@if ($data === null)
			return [[]];
		@else
			<?php $data = json_decode($data)?>
			var objects = [];
			@foreach ($data as $row)
				(function ()
				{
					var object = {};
					@foreach ($row as $name => $value)
						object['{{ $name }}'] = '{{ $value }}';
					@endforeach
					objects.push(object);
				})();
			@endforeach
			return objects;
		@endif
	}
	function handsonAfterChange(changes, source)
	{
		if (source === 'loadData') return;
		var data = this.getData();
		var objects = handsonValuesToObjects(data);
		putSession(JSON.stringify(objects));
	}
	function putSession(value)
	{
		$.ajax({
			url: '{{ url('/catalog/session') }}'
			, method: 'post'
			, data: {
				_token: '{{ csrf_token() }}'
				, value: value
			}
		})
		.always(function (data, xhr, error, thrown)
		{
			console.log(data);
		})
		;
	}
	function handsonValuesToObjects(data)
	{
		var objects = [];
		$.each(data, function (index, values)
		{
			var object = {};
			var i = 0;
			@foreach ($columns as $name => $column)
				object['{{ $name }}'] = values[i++];
			@endforeach 
			objects.push(object);
		});
		return objects;
	}
});
</script>
@endpush