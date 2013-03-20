<?php
/*
	escriure - blog cms
	Copyright (C) 2012 David Martí <neikokz at gmail dot com>

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$start = microtime(true);

require('init.php');

$params = explode('/', trim($_SERVER['REQUEST_URI']));
array_shift($params);

foreach ($params as $k => $v) {
	$params[$k] = urldecode($v);
}

$params[0] = (isset($params[0]) && $params[0] != '') ? $params[0] : 'home';

switch ($params[0]) {
	case 'robots.txt':
	case 'favicon.ico':
	case 'sitemap.xml':
		require(modules_dir.'leftovers.php');
		break;
	case 'rss':
		require(modules_dir.'rss.php');
		break;
	case 'admin':
		require(modules_dir.'admin.php');
		break;
	case 'archive':
		require(modules_dir.'archive.php');
		break;
	case 'page':
	case 'home':
		$module = 'list';
		require(modules_dir.'list.php');
		break;
	case '_':
		require(modules_dir.'static.php');
		break;
	case 'blob':
		require(modules_dir.'blob.php');
		break;
	case 'cms':
		require(modules_dir.'cms.php');
		break;
	default:
		$module = 'post';
		require(modules_dir.'post.php');
}

if (!isset($module)) {
	$module = $params[0];
}