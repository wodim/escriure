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

class Settings {
   const READ = 'SELECT domain, lang, locale, `collate`,
	analytics_enabled, analytics_code,
	url, statics_url, db, title,
	page_size, robots, mail, theme, site_key
	FROM sites WHERE domain = \'%s\'';
	/* we are entitled to add reasonable defaults here!! */
	var $domain = '';
	var $lang = '';
	var $locale = '';
	var $collate = '';
	var $analytics_enabled = false;
	var $analytics_code = '';
	var $url = '';
	var $statics_url = '';
	var $db = '';
	var $title = '';
	var $page_size = 3;
	var $robots = 'allow';
	var $mail = '';
	var $theme = 'oasis';
	var $site_key = '';

	var $read = false;

	function init() {
		global $db;

		$results = $db->get_row(
			sprintf(Settings::READ,
				clean($_SERVER['HTTP_HOST'], 32, true)));

		if (!$results) {
			return $this->read;
		}

		foreach (get_object_vars($results) as $variable => $value) {
			$this->$variable = $value;
		}

		$this->read = true;
		return true;
	}
}
