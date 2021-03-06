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

/* don't index any of these */
header('X-Robots-Tag: noindex');

switch ($params[0]) {
	case 'sitemap.xml':
		header('Content-Type: text/xml; charset=utf-8');
		/* I'm not using LIMIT here, but you may need it. :D */
		$results = $db->get_results('SELECT permaid FROM posts WHERE db = :db AND status = \'published\' ORDER BY id DESC', array(
			array(':db', $settings->db, PDO::PARAM_STR)
		));
		if (!$results) {
			header('HTTP/1.0 404');
			die;
		}
		$archive = sprintf('%sarchive', $settings->url);
		foreach ($results as $post) {
			$posts[]['permalink'] = sprintf('%s%s', $settings->url, $post['permaid']);
		}
		$vars = compact('archive', 'posts');
		Haanga::Load('sitemap.xml', $vars);
		break;
	case 'robots.txt':
		header('Content-Type: text/plain; charset=utf-8');
		echo "User-agent: *\n";
		switch ($settings->robots) {
			case 'disallow':
				echo "Disallow: /";
				break;
			case 'allow':
			default:
				/* don't index /page/: only the individual articles and the post archive */
				echo "Allow: /\nDisallow: /page/*\nDisallow: /rss\n\n";
				printf('Sitemap: %ssitemap.xml', $settings->url);
		}
		break;
	case 'favicon.ico':
		header('HTTP/1.1 301 Moved Permanently');
		header(sprintf('Location: %sescriure.png', $settings->statics_url));
}