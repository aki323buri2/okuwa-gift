@extends('layouts/master')
@push('styles')
<style>
.ui.menu .ui.right.menu
{
	border: none;
}
.ui.menu .search.input > input:first-child
{
	border-radius: 1rem;
	padding-left: 1rem;
}
.ui.menu .search.input > input:first-child + i.icon
{
	right: .3rem;
}
.ui.menu .header.item > img.icon
{
	width: 1.5rem;
	margin-right: .5rem;
}
</style>
@endpush
@section('topbar')
<div class="ui menu">
	<a href="{{ url('/') }}" class="header item">
		<img src='{{ url('/images/okuwa.png') }}' class='icon'>
		オークワギフト
	</a>
	<a href="{{ url('/home') }}" class="item">
		<i class="home icon"></i>
		Home
	</a>

	<div class="ui right menu">
		<div class="item">
			<div class="ui icon input search">
				<input type="text" placeholder="Search">
				<i class="search icon"></i>
			</div>
		</div>
		<a href="#" class="item">
			<i class="user icon"></i>
			Links
		</a>
	</div>
</div>
@endsection