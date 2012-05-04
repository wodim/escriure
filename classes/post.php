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

class Post {
	const READ = 'id, permaid, nick, date, UNIX_TIMESTAMP(date) AS ts, title, text, tags, db, status';
	const READ_BY_PERMAID = 'SELECT id, permaid, nick, date, UNIX_TIMESTAMP(date) AS ts, title, text, db, tags, status FROM posts WHERE permaid = \'%s\' AND db = \'%s\'';

	var $read = false;
	var $id = 0;
	var $permaid = '';
	var $nick = '';
	var $date = '';
	var $title = '';
	var $text = '';
	var $tags = '';
	var $db = '';
	var $status = 'draft';

	var $permalink = '';
	var $hdate = '';

	function read($results = null) {
		global $db, $settings, $session;

		/* we may already have results (eg. when called from list.php)
			but maybe we do not, so fetch from the db */
		if (!$results) {
			/* if we didn't have $results, no id and no permaid, this was a faulty rqeuest. */
			if (!$this->id && !$this->permaid) {
				return false;
			}
			$query = sprintf(Post::READ_BY_PERMAID, clean($this->permaid, 64, true), $settings->db);
			$results = $db->get_row($query);
		}

		/* still no results? return */
		if (!$results) {
			return false;
		}

		foreach (get_object_vars($results) as $variable => $value) {
			$this->$variable = $value;
		}

		$this->permalink = sprintf('%s%s', $settings->url, $this->permaid);
		$this->hdate = strftime(_('%m/%d %I:%M %P'), $this->ts);
		$this->text = str_replace("\n", '', $this->text);
		$this->read = true;
		return true;
	}

	function output($odd = true) {
		global $session, $settings;
		if (!$this->read) {
			die();
		}

		$post = $this;

		$vars = compact('post');
		Haanga::Load(sprintf('%s/post.html', $settings->theme), $vars);

		return true;
	}

	function output_rss() {
		global $session;
		if (!$this->read) {
			die();
		}

		$post = $this;

		$post->ts = date(DATE_RSS, $post->ts);

		$vars = compact('post');
		Haanga::Load('rss-post.html', $vars);

		return true;
	}
}
