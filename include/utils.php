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

function redir($location = null) {
	if (!$location) {
		$location = '/';
	}

	header('HTTP/1.0 302 Found');
	header('Location: '.$location);
	die;
}

function is_posting($required) {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		return false;
	}

	foreach ($required as $variable) {
		if (!isset($_POST[$variable])) {
			return false;
		}
	}

	return true;
}

function debug($message) {
	printf('<span style="border: 1px solid white; background: red; font-weight: bold; font-size: 9pt; color: white; padding: 3px 5px; display: inline-block;">%s</span>%s', $message, "\n");
}

function not_found() {
	header('HTTP/1.0 404 Not Found');
	Haanga::Load('bare404.html');
	die;
}