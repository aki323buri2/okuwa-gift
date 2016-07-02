<?php
$links = [
	'/vendor/font-awesome/css/font-awesome.css', 
	'/vendor/jquery/dist/jquery.js', 
	'/vendor/jquery-ui/jquery-ui.js', 
	'/vendor/jquery-ui/themes/base/jquery-ui.css', 
	// '/vendor/semantic/dist/semantic.css', 
	'/vendor/semantic/dist/semantic.js', 
	'/build/semantic/semantic.css', 
];
$links = array_merge($links, (array)@$links_more);
$links = collect($links)->groupBy(function ($url)
{
	return extname_without_dot($url);
});
$links = $links->toArray();
?>
<!DOCTYPE html>
<html>
<head lang="ja">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>@yield('title')</title>
@foreach ((array)@$links['css'] as $url)
	<link rel="stylesheet" href="{{ url($url) }}">
@endforeach
@foreach ((array)@$links['js'] as $url)
	<script src="{{ url($url) }}"></script>
@endforeach
@stack('styles')
@stack('scripts')
</head>
<body>
<div id="topbar">
	@yield('topbar')
</div>
<div id="sidebar">
	@yield('sidebar')
</div>
<div id="content">
	@yield('content')
</div>
</body>
</html>