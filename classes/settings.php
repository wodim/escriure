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
   const READ = 'SELECT domain, statics_only, lang, locale, `collate`,
	analytics_enabled, analytics_code,
	url, statics_url, db, title,
	page_size, robots, mail, theme, site_key, meta_json,
	admin_mail
	FROM sites WHERE domain = :domain';
	/* we are entitled to add reasonable defaults here!! */
	var $domain = '';
	var $statics_only = false;
	var $lang = '';
	var $locale = '';
	var $collate = '';
	var $analytics_enabled = false;
	var $analytics_code = '';
	var $url = '';
	var $statics_url = '';
	var $db = '';
	var $title = '';
	var $page_size = 5;
	var $robots = 'allow';
	var $mail = '';
	var $theme = 'oasis';
	var $site_key = '';
	var $meta_json = '';
	var $meta = '';
	var $admin_mail = '';

	var $read = false;

	function init() {
		global $db;

		$results = $db->get_row(Settings::READ, array(
			array(':domain', $_SERVER['HTTP_HOST'], PDO::PARAM_STR)
		));

		if (!$results) {
			return $this->read;
		}

		foreach ($results as $variable => $value) {
			$this->$variable = $value;
		}

		$this->meta = @json_decode($this->meta_json);

		$this->read = true;
		return true;
	}
}