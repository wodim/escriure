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

if (isset($params[1]) && is_numeric($params[1])) {
	$page_number = (int)$params[1];
} else {
	$page_number = 1;
}

$posts = $db->get_results(
	sprintf('SELECT %s FROM posts WHERE db = :db AND status = \'published\' ORDER BY id DESC LIMIT %d,%d',
		Post::READ, (--$page_number * $settings->page_size), $settings->page_size), array(
	array(':db', $settings->db, PDO::PARAM_STR)
));

if (!$posts) {
	$html->do_sysmsg(_('Page not found'), null, 404);
}

++$page_number;
$session->canonical = ($page_number == 1) ? $settings->url : sprintf('%spage/%d', $settings->url, $page_number);

$html->do_header();
$rows = $db->get_var('SELECT COUNT(1) FROM posts WHERE db = :db AND status = \'published\'', array(
	array(':db', $settings->db, PDO::PARAM_STR)
));

$pager = $html->do_pages($page_number, ceil($rows / $settings->page_size), '/page/%d', 4);

$post = new Post();
foreach ($posts as $this_post) {
	$post->listing = true;
	$post->read($this_post);
	$post->output();
}

echo($pager);

$html->do_footer();
