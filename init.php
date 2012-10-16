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

$config['site']['include'] = 'include';
$config['site']['modules'] = 'modules';
$config['site']['classes'] = 'classes';

define('include_dir', $config['site']['include'].'/');
define('modules_dir', $config['site']['modules'].'/');
define('classes_dir', $config['site']['classes'].'/');

require(include_dir.'utils.php');

require(include_dir.'ezsql/shared/ez_sql_core.php');
require(include_dir.'ezsql/mysql/ez_sql_mysql.php');

$db = new ezSQL_mysql();

if (!@$db->quick_connect($config['db']['user'], $config['db']['pass'],
	$config['db']['name'], $config['db']['host'])) {
	// ?
	header('HTTP/1.1 500 Internal Server Error');
	die('DBE');
}

$db->query('SET NAMES `utf8`');

require(classes_dir.'settings.php');
$settings = new Settings();
if (!$settings->init()) {
	header('HTTP/1.1 404 Not Found');
	die('Invalid hostname');
}

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

// force https ?
if (isset($_SERVER['HTTPS'])) {
	redir(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_GET['q']));
}
