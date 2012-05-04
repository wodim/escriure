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
	global $session;

	if (!$location) {
		$location = '/';
	}

	header('HTTP/1.0 302 Found');
	header('Location: '.$location);
	die;
}

/* START LEGACY, ie not being used anymroe */
// you could call this function with no arguments to get a mm.
function get_avatar_url($email = '') {
	return('http://www.gravatar.com/avatar/'.md5(trim(strtolower($email))).'/?d=mm');
}

function is_valid_nick($nick) {
	return(preg_match('/^[a-zA-Z0-9]{3,12}$/', $nick));
}

function is_valid_email($email) {
	return(preg_match('/^[a-z0-9\.\-_]+(\+[a-z0-9\.\-_]+)*@[a-z0-9\-\.]+\.[a-z]{2,4}$/i', strtolower($email)));
}

function system_message($code, $message) {
	header('HTTP/1.0 '.$code);
	die('<h3>'.$code.' - '.$message.'</h3>');
}
/* END LEGACY */

function escape($string) {
	// well it's shorter.

	return mysql_real_escape_string($string);
}

function clean($string, $maxlen = 0, $escape = false) {
	$string = $maxlen ? substr(trim($string), 0, $maxlen) : trim($string);

	if ($escape) {
		$string = escape($string);
	}

	return $string;
}

function is_bot() {
	return(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|slurp/i', $_SERVER['HTTP_USER_AGENT']));
}

function sha512($string) {
	return hash('sha512', $string);
}
