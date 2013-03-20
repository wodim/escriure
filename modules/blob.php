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

if (!isset($params[1]) || count($params) != 2) {
	$html->do_sysmsg(_('Page not found'), null, 404);
}

$file = $db->get_row('SELECT name, mimetype, content, size FROM blobs WHERE db = :db AND name = :name', array(
	array(':db', $settings->db, PDO::PARAM_STR),
	array(':name', $params[1], PDO::PARAM_STR)
));

if (!$file) {
	$html->do_sysmsg(_('Page not found'), null, 404);
}

header('Cache-Control: public, max-age=31536000');
header('Expires: Thu, 01 Jan 2099 00:00:00 GMT');
header('Last-Modified: Sun, 01 Jan 2012 00:00:00 GMT');
if ($file['mimetype'] == 'text/plain') {
	header(sprintf('Content-Type: %s; charset=utf-8', $file['mimetype']));
} else {
	header(sprintf('Content-Type: %s', $file['mimetype']));
}
header(sprintf('Content-Length: %d', $file['size']));

echo $file['content'];
die; /* otherwise, the queries counter would be shown. */