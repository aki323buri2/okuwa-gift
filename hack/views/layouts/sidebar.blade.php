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
sidebar..
@endsection