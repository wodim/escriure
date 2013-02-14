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

require('config.php');
if (file_exists('local.php')) {
	require('local.php');
}

$config['site']['include'] = 'include';
$config['site']['modules'] = 'modules';
$config['site']['classes'] = 'classes';

define('include_dir', $config['site']['include'].'/');
define('modules_dir', $config['site']['modules'].'/');
define('classes_dir', $config['site']['classes'].'/');

require(include_dir.'utils.php');

/* initialise db */
require(classes_dir.'db.php');
$db = new DB();
$db->type = $config['db']['type'];
$db->debug = $config['db']['debug'];
$db->persistent = $config['db']['persistent'];
$db->file = $config['db']['file'];
$db->user = $config['db']['user'];
$db->pass = $config['db']['pass'];
$db->name = $config['db']['name'];
$db->socket = $config['db']['socket'];
$db->host = $config['db']['host'];
if (!$db->init()) {
	header('HTTP/1.1 500 Internal Server Error');
	die('DBE');
}

require(classes_dir.'settings.php');
$settings = new Settings();
if (!$settings->init()) {
	header('HTTP/1.1 500 Internal Server Error');
	die('VHE');
}

/* encoding */
if ($db->type == 'mysql') {
	$db->query(sprintf('SET NAMES utf8 COLLATE %s', $settings->collate));
}
mb_internal_encoding('utf8');

// initialize Haanga
require(include_dir.'Haanga.php');
Haanga::configure(array(
	'template_dir' => 'templates/',
	'cache_dir' => 'templates/compiled/',
	'compiler' => array(
		'global' => array('settings', 'session', 'html'),
		'strip_whitespace' => true,
		'allow_exec' => false,
		'autoescape' => false
	)
));

// initialize the html engine
require(classes_dir.'html.php');
$html = new HTML();
$html->init();

// initiailze session
require(classes_dir.'session.php');
$session = new Session();
$session->init();

// configure gettext's locale
putenv('LC_ALL='.$settings->locale);
setlocale(LC_ALL, $settings->locale);
bindtextdomain('messages', './locale');
textdomain('messages');