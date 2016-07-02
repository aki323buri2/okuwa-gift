<?php
function nv($value, $backup = null)
{
	return isset($value) ? $value : $backup;
}
function path_join(...$paths)
{
	$join = '';
	$glue = DIRECTORY_SEPARATOR;
	foreach ($paths as $path)
	{
		$join = rtrim($join, $glue) . $glue . ltrim($path, $glue);
	}
	return $join;
}
function extname($path)
{
	return preg_match('/[.][^.]+$/', $path, $match) ? $match[0] : '';
}
function extname_without_dot($path)
{
	return preg_replace('/^[.]/', '', extname($path));
}
function rename_extension($path, $extname)
{
	if (strlen($extname) && $extname[0] !== '.') $extname = '.' . $extname;	
	return preg_replace('/([.][^.]+)?$/', $extname, $path, 1);
}
function remove_extension($path)
{
	return rename_extension($path, '');
}