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
	const READ = 'id, nick, mail, url, timestamp,  ip, text, post_id, parent, db, reply';
	const READ_BY_ID = 'SELECT id, nick, mail, url, timestamp, ip, text, post_id, parent, db, reply FROM comments WHERE id = \'%s\' AND db = \'%s\'';
	const READ_BY_POST_ID = 'SELECT id, nick, mail, url, timestamp, ip, text, post_id, parent, db, reply FROM comments WHERE post_id = \'%s\' AND db = \'%s\'';

	var $read = false;
	var $id = 0;
	var $nick = '';
	var $mail = '';
	var $url = '';
	var $timestamp = '';
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
	var $post = '';

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

		foreach ($results as $variable => $value) {
			$this->$variable = $value;
		}

		/* $this->avatar = get_avatar_url($this->mail, 32); */
		$this->url_safe = htmlentities($this->url);
		if (!preg_match('/^https?:\/\//', $this->url_safe)) {
			$this->url_safe = sprintf('http://%s', $this->url_safe);
		}
		$this->hdate = strftime(_('%m/%d %I:%M %P'), $this->timestamp);
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
		global $db, $settings, $session;

		$db->query('INSERT INTO comments (nick, mail, url, timestamp, ip, text, post_id, parent, db, status)
			VALUES (:nick, :mail, :url, :timestamp, :ip, :text, :post_id, :parent, :db, :status)', array(
			array(':nick', $this->nick, PDO::PARAM_STR),
			array(':mail', $this->mail, PDO::PARAM_STR),
			array(':url', $this->url, PDO::PARAM_STR),
			array(':timestamp', time(), PDO::PARAM_INT),
			array(':ip', $session->ip, PDO::PARAM_STR),
			array(':text', $this->text, PDO::PARAM_STR),
			array(':post_id', $this->post_id, PDO::PARAM_INT),
			array(':parent', $this->parent, PDO::PARAM_INT),
			array(':db', $settings->db, PDO::PARAM_STR),
			array(':status', $this->status, PDO::PARAM_STR)
		));
		$db->query('UPDATE posts SET comment_count = comment_count + 1 WHERE id = :id', array(
			array(':id', $this->post_id, PDO::PARAM_INT)
		));

		// send mail
		if ($settings->admin_mail) {
			$mail = sprintf(
				'%s has posted a new comment on "%s":'."\n\n".
				'%s'."\n\n".
				'You can read it here:'."\n".
				'%s%s#comments',
				$nick, $this->post->title, $this->text, $settings->url, $this->post->permaid);
			$mail = wordwrap($mail, 70);
			mail(sprintf('%s admin <%s>', $settings->title, $settings->admin_mail),
				sprintf('[%s] New comment on %s', $settings->title, $this->post->title),
				$mail,
				sprintf("From: %s <%s>\nReturn-Path: <%s>",
					$settings->title, $settings->mail, $settings->mail),
				sprintf('-f%s', $settings->mail));
		}
		return true;
	}
}