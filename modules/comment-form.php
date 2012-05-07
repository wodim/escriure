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
$kind = rand(0, 2);
switch ($kind) {
	case 0: // add
		$first = rand(0, 7);
		$second = rand(0, 7);
		$result = $first + $second;
		$captcha = sprintf('%s + %s', $first, $second);
		break;
	case 1: // sub
		$first = rand(0, 10);
		$second = rand(0, $first);
		$result = $first - $second;
		$captcha = sprintf('%s - %s', $first, $second);
		break;
	case 2: // mul
		$first = rand(0, 4);
		$second = rand(0, 4);
		$result = $first * $second;
		$captcha = sprintf('%s × %s', $first, $second);
}

$form['seed'] = rand();
$form['auth'] = md5(sprintf('%s%s%s', $result, $settings->site_key, $form['seed']));
$form['captcha'] = sprintf(_('Result of <strong>%s</strong>?'), $captcha);

/* we need post for post.wraning */
$vars = compact('form', 'post');
Haanga::Load(sprintf('%s/comment-form.html', $settings->theme), $vars);
