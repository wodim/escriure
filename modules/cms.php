<?php
/*
	escriure - blog cms
	Copyright (C) 2013 David Martí <neikokz at gmail dot com>

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

$html->do_sysmsg(_('Forbidden'), null, 403);

$type = isset($_POST['type']) ? $_POST['type'] : null;
$results = array();
$info = null;
$error = false;

function add_result($text) {
	global $error, $results; // shit

	$error = true;
	$results[] = $text;
}

switch ($type) {
	case 'blob_insert':
		/* insert new blobs */
		if (!isset($_FILES['content'])) {
			add_result('You did not choose the file to upload.');
		}

		if ($_FILES['content']['error'] != UPLOAD_ERR_OK) {
			add_result(sprintf('Failed to upload the file: %d', $_FILES['content']['error']));
		}

		if ($error == false) {
			$md5 = md5(sprintf('%s%s', file_get_contents($_FILES['content']['tmp_name']), $settings->site_key));
			$count = $db->get_var('SELECT COUNT(1) FROM blobs WHERE name = :name', array(
				array(':name', $md5, PDO::PARAM_STR)
			));
			if ($count > 0) {
				add_result(sprintf('Error: duplicated file. Old name: <a href="%sblob/%s" target="_blank"><code>%s</code></a>', $settings->url, $md5, $md5));
			} else {
				$db->query('INSERT INTO blobs (name, mimetype, `timestamp`, size, content)
					VALUES (:name, :mimetype, :timestamp, :size, :content)', array(
					array(':name', $md5, PDO::PARAM_STR),
					array(':mimetype',
						isset($_POST['mimetype']) && trim($_POST['mimetype'] != '') ? trim($_POST['mimetype']) : $_FILES['content']['type'],
						PDO::PARAM_STR),
					array(':timestamp', time(), PDO::PARAM_INT),
					array(':size', $_FILES['content']['size'], PDO::PARAM_INT),
					array(':content', file_get_contents($_FILES['content']['tmp_name']), PDO::PARAM_LOB)
				));
				add_result(sprintf('File saved: <a href="%sblob/%s" target="_blank"><code>%s</code></a>', $settings->url, $md5, $md5));
			}
		}
		break;
	case 'blob_list':
		$return = $db->get_results('SELECT name, timestamp, mimetype, size FROM blobs');
		if (count($return) > 0) {
			foreach ($return as $blob) {
				add_result(
					sprintf('<a href="%sblob/%s" target="_blank"><code>%s</code></a> ─ %s ─ %d KB ─ added on %s',
						$settings->url, $blob['name'], $blob['name'], $blob['mimetype'], $blob['size'] / 1024, date(DATE_RFC822, $blob['timestamp']))
				);
			}
		} else {
			add_result('No files stored.');
		}
		break;
	default:
		break;
}

if (count($results) > 0) {
	$info = sprintf('Information:', $info);
	foreach ($results as $result) {
		$info = sprintf('%s<li>%s</li>', $info, $result);
	}
}

$vars = compact('info');
Haanga::Load('cms.html', $vars);