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

global $params, $db, $html;

if (!isset($params[1])) {
	$params[1] = 'index';
}

function all_posts() {
	global $db;

	$results = $db->get_results('SELECT id FROM posts');
	
	foreach ($results as $result) {
		$return[] = $result['id'];
	}
	
	return $return;
}

switch ($params[1]) {
	/* example modules. */
	case 'template':
		/* shows a template; you can copy it to your hard disk and write the post there,
			then dump it to phpmyadmin to publish it */
		$html->do_header();
		$post = new Post();
		$post->read = true;
		$post->text = "\n\n\n<!-- POST CONTENT -->\n\n\n";
		$post->output();
		$html->do_footer();
		break;
	case 'recount':
		/* recounts all comments. this should be used whenever you delete or insert a comment
			by hand */
		/* we will probably never have a save() method for Post, but, in case we have
			one, fix this */
		die;
		header('Content-type: text/plain');
		foreach (all_posts() as $post_id) {
			printf("--- Counting comments for %d...\n", $post_id);
			$comment_count = $db->get_var('SELECT COUNT(0) FROM comments WHERE post_id = :id AND status = \'shown\'', array(
				array(':id', $post_id, PDO::PARAM_INT)
			));
			printf("+++ %d comments found for %d\n", $comment_count, $post_id);
			printf("--- Updating comment_count for %d...\n", $post_id);
			$db->query('UPDATE posts SET comment_count = :comment_count WHERE id = :id', array(
				array(':comment_count', $comment_count, PDO::PARAM_INT),
				array(':id', $post_id, PDO::PARAM_INT)
			));
			printf("+++ Updated comment_count for %d\n", $post_id);
		}
		break;
	case 'timestamp':
		/* convert datetime to unix timestamps */
		die;
		$posts = $db->get_results('SELECT id, UNIX_TIMESTAMP(date) AS ts FROM posts ORDER BY id');
		foreach ($posts as $post) {
			$db->query('UPDATE posts SET timestamp = :timestamp WHERE id = :id', array(
				array(':timestamp', $post['ts'], PDO::PARAM_STR),
				array(':id', $post['id'], PDO::PARAM_STR)
			));
			printf("Updated post %s\n", $post['id']);
		}
		$comments = $db->get_results('SELECT id, UNIX_TIMESTAMP(date) AS ts FROM comments ORDER BY id');
		foreach ($comments as $comment) {
			$db->query('UPDATE comments SET timestamp = :timestamp WHERE id = :id', array(
				array(':timestamp', $comment['ts'], PDO::PARAM_STR),
				array(':id', $comment['id'], PDO::PARAM_STR)
			));
			printf("Updated comment %s\n", $comment['id']);
		}
		printf("End after %d queries\n", $db->num_queries);
		break;
}
