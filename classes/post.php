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
	const READ = 'id, permaid, nick, timestamp, title, text_html, text_markdown, text_type, tags, db, status, comment_count, comment_status';
	const READ_BY_PERMAID = 'SELECT id, permaid, nick, timestamp, title, text_html, text_markdown, text_type, db, tags, status, comment_count, comment_status FROM posts WHERE permaid = :permaid AND db = :db';
	const PREV_POST = 'SELECT permaid, title FROM posts WHERE id < :id AND db = :db AND status = \'published\' ORDER BY id DESC LIMIT 1';
	const NEXT_POST = 'SELECT permaid, title FROM posts WHERE id > :id AND db = :db AND status = \'published\' ORDER BY id ASC LIMIT 1';

	var $read = false;
	var $id = 0;
	var $permaid = '';
	var $nick = '';
	var $timestamp = '';
	var $title = '';
	var $text_html = '';
	var $text_markdown = '';
	var $text_type = 'html';
	var $tags = '';
	var $db = '';
	var $status = 'draft';
	var $comment_count = 0;
	var $comment_status = 'open';

	var $permalink = '';
	var $hdate = '';
	/* shows a warning such as "comments closed", "mail reuiqred" */
	var $warning = '';
	var $listing = false;
	var $cdate = null;
	var $nav_buttons = false;

	function read($results = null) {
		global $db, $settings, $session, $html, $module, $markdown;

		/* we may already have results (eg. when called from list.php)
			but maybe we do not, so fetch from the db */
		if (!$results) {
			/* if we didn't have $results, no id and no permaid, this was a faulty rqeuest. */
			if (!$this->id && !$this->permaid) {
				return false;
			}
			$results = $db->get_row(Post::READ_BY_PERMAID, array(
				array('permaid', $this->permaid, PDO::PARAM_STR),
				array('db', $settings->db, PDO::PARAM_STR)
			));
		}

		/* still no results? return */
		if (!$results) {
			return false;
		}

		foreach ($results as $variable => $value) {
			$this->$variable = $value;
		}

		$this->permalink = sprintf('%s%s', $settings->url, $this->permaid);
		$this->hdate = strftime(_('%m/%d %I:%M %P'), $this->timestamp);
		if ($html->theme_req->custom_dates) {
			$this->cdate = new stdClass();
			$this->populate_cdate();
		}
		if ($html->theme_req->nav_buttons && $module == 'post') {
			$html->theme_req->nav_buttons->available = true;
			$html->theme_req->nav_buttons->prev = $html->theme_req->nav_buttons->next = null;

			$prev = $db->get_row(Post::PREV_POST, array(
				array(':id', $this->id, PDO::PARAM_INT),
				array(':db', $this->db, PDO::PARAM_STR)
			));
			if ($prev) {
				$html->theme_req->nav_buttons->prev = new stdClass();
				$html->theme_req->nav_buttons->prev->permaid = $prev['permaid'];
				$html->theme_req->nav_buttons->prev->title = $prev['title'];
			}

			$next = $db->get_row(Post::NEXT_POST, array(
				array(':id', $this->id, PDO::PARAM_INT),
				array(':db', $this->db, PDO::PARAM_STR)
			));
			if ($next) {
				$html->theme_req->nav_buttons->next = new stdClass();
				$html->theme_req->nav_buttons->next->permaid = $next['permaid'];
				$html->theme_req->nav_buttons->next->title = $next['title'];
			}
		}

		/* markdown conversion */
		if ($this->text_type == 'markdown') {
			$this->text_html = $markdown->render($this->text_markdown);
			$db->query('UPDATE posts SET text_html = :text_html, text_type = \'html\' WHERE id = :id', array(
				array(':text_html', $this->text_html, PDO::PARAM_STR),
				array(':id', $this->id, PDO::PARAM_INT)
			));
		}

		/* lol crap */
		$no_unescape = array('lt', 'gt', 'amp');

		foreach ($no_unescape as $entity) {
			$this->title = str_replace(sprintf('&%s;', $entity), sprintf('{{UH-UH-%s-CANT-TOUCH-THIS}}', $entity), $this->title);
		}
		$this->title = html_entity_decode($this->title, ENT_QUOTES, 'utf-8');
		foreach ($no_unescape as $entity) {
			$this->title = str_replace(sprintf('{{UH-UH-%s-CANT-TOUCH-THIS}}', $entity), sprintf('&%s;', $entity), $this->title);
		}

		foreach ($no_unescape as $entity) {
			$this->text_html = str_replace(sprintf('&%s;', $entity), sprintf('{{UH-UH-%s-CANT-TOUCH-THIS}}', $entity), $this->text_html);
		}
		$this->text_html = html_entity_decode($this->text_html, ENT_QUOTES, 'utf-8');
		foreach ($no_unescape as $entity) {
			$this->text_html = str_replace(sprintf('{{UH-UH-%s-CANT-TOUCH-THIS}}', $entity), sprintf('&%s;', $entity), $this->text_html);
		}

		/* $this->text_html = str_replace(array("\n", "\r"), '', $this->text_html); */
		if ($this->comment_status == 'closed') {
			$this->warning = _('Comments for this post are closed.');
		}
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
		$post->timestamp_rss = date(DATE_RSS, $post->timestamp);
		$post->title = htmlspecialchars($post->title);
		$post->text_rss = htmlspecialchars($post->text_html);

		$vars = compact('post');
		Haanga::Load('rss-post.html', $vars);

		return true;
	}

	function add_warning($text) {
		if ($this->warning != '') {
			$this->warning = sprintf('%s<br />%s', $this->warning, $text);
		} else {
			$this->warning = $text;
		}
	}

	function populate_cdate() {
		global $html;

		$formats = str_split($html->theme_req->custom_dates);
		foreach ($formats as $format) {
			$this->cdate->$format = strftime(sprintf('%%%s', $format), $this->timestamp);
		}
	}
}
