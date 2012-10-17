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

class Comment {
	const READ = 'id, nick, mail, url, date, UNIX_TIMESTAMP(date) AS ts, ip, text, post_id, parent, db, reply';
	const READ_BY_ID = 'SELECT id, nick, mail, url, date, UNIX_TIMESTAMP(date) AS ts, ip, text, post_id, parent, db, reply FROM comments WHERE id = \'%s\' AND db = \'%s\'';
	const READ_BY_POST_ID = 'SELECT id, nick, mail, url, date, UNIX_TIMESTAMP(date) AS ts, ip, text, post_id, parent, db, reply FROM comments WHERE post_id = \'%s\' AND db = \'%s\'';

	var $read = false;
	var $id = 0;
	var $nick = '';
	var $mail = '';
	var $url = '';
	var $date = '';
	var $ts = 0;
	var $ip = '';
	var $text = '';
	var $post_id = 0;
	var $parent = 0;
	var $db = '';
	var $reply = '';

	var $avatar = '';
	var $url_safe = '';
	var $hdate = '';
	var $order = '';
	var $poster = '';

	function read($results = null) {
		global $db, $settings, $session;

		if (!$results) {
			if (!$this->id && !$this->post_id) {
				return false;
			}
			$query = sprintf(Comment::READ_BY_ID, (int)$this->id, $settings->db);
			$results = $db->get_row($query);
		}

		/* still no results? return */
		if (!$results) {
			return false;
		}

		foreach (get_object_vars($results) as $variable => $value) {
			$this->$variable = $value;
		}

		$this->avatar = get_avatar_url($this->mail, 32);
		$this->url_safe = htmlentities($this->url);
		if (!preg_match('/^https?:\/\//', $this->url_safe)) {
			$this->url_safe = sprintf('http://%s', $this->url_safe);
		}
		$this->hdate = strftime(_('%m/%d %I:%M %P'), $this->ts);
		$this->read = true;
		return true;
	}

	function output($odd = true) {
		global $session, $settings;
		if (!$this->read) {
			die();
		}

		$comment = $this;
		$comment->text = $this->text_clean($comment->text);

		$vars = compact('comment');

		Haanga::Load(sprintf('%s/comment.html', $settings->theme), $vars);

		return true;
	}

	function text_clean($text) {
		$text = htmlspecialchars($text);
		$text = str_replace("\n", '<br />', $text);

		return $text;
	}

	function store() {
		global $db, $settings;

		$nick = clean($this->nick, 16, true);
		$mail = clean($this->mail, 64, true);
		$url = clean($this->url, 128, true);
		$text = clean($this->text, 1000, true);
		$post_id = (int)$this->post_id;
		$parent = (int)$this->parent;

		$db->query(sprintf(
			'INSERT INTO comments (nick, mail, url, date, ip, text, post_id, parent, db, status)
				VALUES (\'%s\', \'%s\', \'%s\', NOW(), \'%s\', \'%s\', \'%d\', \'%d\', \'%s\', \'%s\')',
			$nick, $mail, $url, $this->ip, $text, $post_id, $parent, $settings->db, $this->status));
		$db->query(sprintf('UPDATE posts SET comment_count = comment_count + 1 WHERE id = %d', 
			$post_id));

		return true;
	}
}
