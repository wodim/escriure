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

require(classes_dir.'post.php');
require(classes_dir.'comment.php');

global $params, $session;

$post = new post();
preg_match('/^[a-z0-9\-]*/', $params[0], $matches);
$post->permaid = $matches[0];

/* tell google not to index this when it has a query string */
if ($post->permaid != $params[0] || count($params) > 1) {
	header('X-Robots-Tag: noindex');
}

if (!$post->read() || $post->status != 'published' || $post->db != $settings->db) {
	$html->do_sysmsg(_('No such post'), null, 404);
}

if (is_posting(array('nick', 'mail', 'captcha', 'auth', 'seed', 'text'))) {
	require(modules_dir.'comment-store.php');
}

$session->tags = $post->tags;
$html->do_header($post->title);
$post->output();

if ($post->comment_status != 'hidden') {
	require(modules_dir.'comment-list.php');
}

if ($post->comment_status == 'open') {
	$form['action'] = sprintf('/%s#comment-form', $post->permaid);
	require(modules_dir.'comment-form.php');
}

$html->do_footer();
