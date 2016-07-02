@extends('layouts/topbar')
@push('styles')
<style>
#topbar
{
	margin-bottom: 1rem;
}
#sidebar 
{
	float: left;
	width: 16rem;
}
#content
{
	margin-left: 16rem;
}
</style>
@endpush
@section('sidebar')
<div class="ui vertical menu">
	@foreach (range(1, 10) as $no)
		<a href="#" class="item" id="sidemenu{{ $no }}">
			menu {{ $no }}
		</a>
	@endforeach
</div>
@endsection