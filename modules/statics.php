<?php
/*
	escriure - blog cms
	Copyright (C) 2013 David Martí <neikokz at gmail dot com>

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

$statics = array();

function notfound() {
	header('HTTP/1.0 404 Not Found');
	Haanga::Load('bare404.html');
	die();
}

function headers($contenttype) {
	header('Cache-Control: public, max-age=31536000');
	header('Expires: Thu, 01 Jan 2099 00:00:00 GMT');
	header('Last-Modified: Sun, 01 Jan 2012 00:00:00 GMT');
	header(sprintf('Content-Type: %s; charset=utf-8', $contenttype));
}

if (count($params) < 3) {
	notfound();
}

if (!preg_match('/^[a-z_]+$/', $params[2])) {
	notfound();
}

/* css */
if ($params[1] == 'css') {
	if (!file_exists(sprintf('statics/%s/%s.css', $settings->theme, $params[2])) &&
		!file_exists(sprintf('statics/private/%s.css', $params[2]))) {
		notfound();
	}

	foreach ($statics as $static) {
		/* we don't control whether these files exist, the list is hardcoded ^ */
		$timestamp[$static] = md5(sprintf('%s%s', filemtime(sprintf('statics/%s/%s.png', $settings->theme, $static)), $settings->site_key));
	}

	headers('text/css');

	$vars = compact('timestamp');
	if (file_exists(sprintf('statics/%s/%s.css', $settings->theme, $params[2]))) {
		Haanga::Load(sprintf('../statics/%s/%s.css', $settings->theme, $params[2]), $vars);
	} elseif (file_exists(sprintf('statics/private/%s.css', $params[2]))) {
		Haanga::Load(sprintf('../statics/private/%s.css', $params[2]), $vars);
	}
}

if ($params[1] == 'png') {
	if (!file_exists(sprintf('statics/%s/%s.png', $settings->theme, $params[2])) &&
		!file_exists(sprintf('statics/private/%s.png', $params[2]))) {
		notfound();
	}

	headers('image/png');

	if (file_exists(sprintf('statics/%s/%s.png', $settings->theme, $params[2]))) {
		readfile(sprintf('statics/%s/%s.png', $settings->theme, $params[2]));
	} elseif (file_exists(sprintf('statics/private/%s.png', $params[2]))) {
		readfile(sprintf('statics/private/%s.png', $params[2]));
	}
}

if ($params[1] == 'js') {
	if (!file_exists(sprintf('statics/%s.js', $params[2])) &&
		!file_exists(sprintf('statics/private/%s.js', $params[2]))) {
		notfound();
	}

	headers('application/javascript');

	if (file_exists(sprintf('statics/%s.js', $params[2]))) {
		readfile(sprintf('statics/%s.js', $params[2]));
	} elseif (file_exists(sprintf('statics/private/%s.js', $params[2]))) {
		readfile(sprintf('statics/private/%s.js', $params[2]));
	}
}

die(); /* otherwise, the queries counter would be shown. */