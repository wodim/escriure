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

class HTML {
	var $theme_req = null;

	function init() {
		global $settings, $db;

		/* since we want to be able to know whether these have been enabled,
			(for example, from templates, where we can't use isset),
			define them */
		$this->theme_req = new stdClass();
		// list of latest posts
		$this->theme_req->latest_posts = false;
		// custom dates, as in strftime();
		$this->theme_req->custom_dates = false;
		// previous/next navigation buttons for each post
		$this->theme_req->nav_buttons = false;
		// html4 compatible (this is, no html5 tags)
		$this->theme_req->html4_compat = false;

		$file = sprintf('templates/%s/features.php', $settings->theme);
		if (file_exists($file)) {
			require($file);
		} else {
			return;
		}

		foreach ($features as $feature => $value) {
			switch ($feature) {
				case 'latest_posts':
					if ($value == true) {
						$this->theme_req->latest_posts =
							$db->get_results(
								sprintf('SELECT permaid, title FROM posts WHERE db = :db AND status = \'published\' ORDER BY id DESC LIMIT %d',
									$settings->page_size * 2), array(
								array(':db', $settings->db, PDO::PARAM_STR)
							));
					} else {
						$this->theme_req->latest_posts = null;
					}
					break;
				case 'custom_dates':
					$this->theme_req->custom_dates = $value;
					break;
				case 'nav_buttons':
					$this->theme_req->nav_buttons = new stdClass();
					break;
				case 'html4_compat':
					$this->theme_req->html4_compat = $value;
					break;
				case 'comments':
					$this->theme_req->comments = $value;
					break;
			}
		}
	}

	function do_header($title = null) {
		global $session, $settings;

		header('Content-Type: text/html; charset=utf-8');
		if ($this->theme_req->html4_compat == false) {
			$this->check_browser();
		}
		$timestamp['core'] = md5(sprintf('%s%s%s', filemtime(sprintf('statics/%s/core.css', $settings->theme)), $settings->site_key, $settings->theme));
		$timestamp['escriure'] = md5(sprintf('%s%s%s', filemtime(sprintf('statics/%s/escriure.png', $settings->theme)), $settings->site_key, $settings->theme));
		if ($settings->analytics_enabled) {
			$timestamp['ga'] = md5(sprintf('%s%s', filemtime('statics/ga.js'), $settings->site_key));
		}
		$vars = compact('title', 'timestamp');
		Haanga::Load(sprintf('%s/header.html', $settings->theme), $vars);
	}

	function do_footer() {
		global $start, $db, $session, $settings;

		Haanga::Load(sprintf('%s/footer.html', $settings->theme));
		printf('<!-- %.4f - %d -->', (microtime(true) - $start), $db->num_queries);
	}

	function do_pages($page = 1, $total_pages, $query, $adjacents = 3) {
		if ($total_pages < 2) {
			return;
		}

		$dots = false;

		$pager = '<div class="pager">';
		if ($page == 1) {
			$pager .= '<span>'._('« prev').'</span>';
		} else {
			$pager .= '<a href="'.sprintf($query, $page - 1).'">'._('« prev').'</a>';
		}

		for ($i = 1; $i < $total_pages + 1; $i++) {
			if ($i == 1 || $adjacents > abs($page - $i)) {
				if ($i == $page) {
					$pager .= '<span class="current">'.$i.'</span>';
				} else {
					$pager .= '<a href="'.sprintf($query, $i).'">'.$i.'</a>';
				}
				$dots = false;
			} else {
				if (!$dots) {
					$pager .= '<span>...</span>';
				}
				$dots = true;
			}
		}

		if ($page == $total_pages) {
			$pager .= '<span>'._('next »').'</span>';
		} else {
			$pager .= '<a href="'.sprintf($query, $page + 1).'">'._('next »').'</a>';
		}
		return $pager.'</div>';
	}

	function do_sysmsg($title, $message, $code) {
		global $session, $settings;

		$session->sysmsg = true;

		header('HTTP/1.1 '.$code);
		$this->do_header($title);
		if (!$message) {
			$message = _('Are you lost?');
		}

		$vars = compact('title', 'message');
		Haanga::Load(sprintf('%s/sysmsg.html', $settings->theme), $vars);
		$this->do_footer();
		die();
	}

	function check_browser() {
		global $settings;

		if (!isset($_SERVER['HTTP_USER_AGENT'])) {
			return;
		}

		if (!preg_match('/MSIE\s[678]/', $_SERVER['HTTP_USER_AGENT'])) {
			return;
		}

		Haanga::Load(sprintf('%s/unsupported-browser.html', $settings->theme));
		die;
	}
}