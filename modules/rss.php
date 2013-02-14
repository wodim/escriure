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

global $params, $settings;

$posts = $db->get_results(
	sprintf('SELECT %s FROM posts WHERE status = \'published\' AND db = :db ORDER BY id DESC LIMIT %d', Post::READ, $settings->page_size), array(
	array(':db', $settings->db, PDO::PARAM_STR)
));

if (!$posts) {
	header('HTTP/1.1 404');
	die;
}

$rss['date'] = date('r', $posts[0]->ts);

$vars = compact('rss');

header('Content-type: text/xml; charset=utf-8');
header('X-Robots-Tag: noindex');
Haanga::Load('rss-header.html', $vars);

$post = new post();
foreach ($posts as $this_post) {
	$post->read($this_post);
	$post->output_rss();
}

Haanga::Load('rss-footer.html');
