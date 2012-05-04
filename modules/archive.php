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

$html->do_header(_('Post archive'));

$posts = $db->get_results(
	sprintf('SELECT UNIX_TIMESTAMP(date) AS ts, title, permaid FROM posts WHERE db = \'%s\' AND status = \'published\' ORDER BY date DESC',
		$settings->db));

if (!$posts) {
	$html->do_sysmsg(_('Page not found'), null, 404);
}

$timestamp = array();
$post = new post();
echo '<section class="archive">';
foreach ($posts as $this_post) {
	$post->read($this_post);
	if (!isset($timestamp['00'.date('Y', $post->ts)])) {
		$timestamp['00'.date('Y', $post->ts)] = true;
		printf('<h3>%s</h3>', date('Y', $post->ts));
	}
	if (!isset($timestamp[date('mY', $post->ts)])) {
		$timestamp[date('mY', $post->ts)] = true;
		printf('<h4>%s</h4>', strftime('%B', $post->ts));
	}
	printf('<p><a href="%s%s">%s</a> - <strong>%s</strong>', $settings->url, $post->permaid, $post->title, strftime(_('%B %e'), $post->ts));
}
echo '</section>';

$html->do_footer();
