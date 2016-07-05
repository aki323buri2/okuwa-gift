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
	width: auto;
}
@foreach ($columns as $name => $column)
	#table1 th.{{ $name }} { width: {{ $column->width }}; }
@endforeach
#table1 th
{
	text-align: center;
}
#table1 tbody td.nouka, 
#table1 tbody td.baika, 
#table1 tbody td.stanka
{
	text-align: right;
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
		@foreach ($data as $row)
			<tr>
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
			</tr>
		@endforeach
	</tbody>
</table>
@endsection