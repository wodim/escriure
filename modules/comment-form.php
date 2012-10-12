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

/* generate captcha */

function scramble($what) {
	$scramble = array(
		0 => array('0', '&#48;'),
		1 => array('1', '&#49;'),
		2 => array('2', '&#50;'),
		3 => array('3', '&#51;'),
		4 => array('4', '&#52;'),
		5 => array('5', '&#53;'),
		6 => array('6', '&#54;'),
		7 => array('7', '&#55;'),
		8 => array('8', '&#56;'),
		9 => array('9', '&#57;'),
		10 => array('10', '&#49;&#48;'),
		'add' => array('+', '&#43;'),
		'sub' => array('+', '&#45;'),
		'mul' => array('×', '&#215;', '&times;')
	);
	return $scramble[$what][rand(0, count($scramble[$what]) - 1)];
}

$kind = rand(0, 2);
switch ($kind) {
	case 0: // add
		$first = rand(0, 7);
		$second = rand(0, 7);
		$operation = 'add';
		$result = $first + $second;
		break;
	case 1: // sub
		$first = rand(0, 10);
		$second = rand(0, $first);
		$operation = 'sub';
		$result = $first - $second;
		break;
	case 2: // mul
		$first = rand(0, 4);
		$second = rand(0, 4);
		$operation = 'mul';
		$result = $first * $second;
}

$captcha = sprintf('%s %s %s', scramble($first), scramble($operation), scramble($second));

$form['seed'] = rand();
$form['auth'] = md5(sprintf('%s%s%s', $result, $settings->site_key, $form['seed']));
$form['captcha'] = sprintf(_('Result of <strong>%s</strong>?'), $captcha);

/* we need post for post.wraning */
$vars = compact('form', 'post');
Haanga::Load(sprintf('%s/comment-form.html', $settings->theme), $vars);
