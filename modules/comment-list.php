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

$comments = $db->get_results(
	sprintf('SELECT %s FROM comments WHERE db = :db AND status = \'shown\' AND post_id = :post_id ORDER BY id ASC', Comment::READ), array(
	array(':db', $settings->db, PDO::PARAM_STR),
	array(':post_id', $post->id, PDO::PARAM_INT)
));

if ($comments) {
	$comment = new Comment();
	/* anchor */
	echo '<a id="comments"></a>';
	foreach ($comments as $this_comment) {
		$comment->read($this_comment);
		++$comment->order;
		$comment->post = $post;
		$comment->output();
	}
}
