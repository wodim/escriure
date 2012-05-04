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

global $params, $db;

if (!isset($params[1])) {
	$params[1] = 'index';
}

function all_posts() {
	global $db;

	$results = $db->get_results('SELECT id FROM posts');
	
	foreach ($results as $result) {
		$return[] = $result->id;
	}
	
	return $return;
}

switch ($params[1]) {
	/* example modules. */
	case 'fill':
		for ($i = 0; $i < 100; $i++) {
			$db->query(sprintf("INSERT INTO posts (permaid, nick, date, title, text, tags, db, status)
				VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 'escriure', 'published')",
				sprintf('post-numero-%d', rand(0, 10000)),
				'wodim',
				sprintf('%s-%s-%s %s:%s:%s', rand(2007, 2012), rand(1, 12), rand(1, 28), rand(0, 23), rand(0, 59), rand(0, 59)),
				'Title',
				'Text',
				'tags, of, the, post'));
		}
}
