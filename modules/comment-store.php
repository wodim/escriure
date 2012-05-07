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

if ($post->comment_status == 'closed') {
	$post->add_warning(_('Comments for this post were closed while you were writing this one.'));
}

/* trim but don't clean here */
$nick = substr(trim($_POST['nick']), 0, 32);
$mail = substr(trim($_POST['mail']), 0, 64);
$ip = $session->ip;
$text = substr($_POST['text'], 0, 10000);
$url = substr($_POST['url'], 0, 128);
/* inherited from the prev source */
$post_id = $post->id;

/* checks.. */
if (!$nick) {
	$post->add_warning(_('Please, don\'t leave your name empty. You can use an alias if you want.'));
}
if ($mail && !is_valid_email($mail)) {
	$post->add_warning(_('Your e-mail address is not valid. Please, check it and try again or just leave it empty.'));
}
if (!$text) {
	$post->add_warning(_('You forgot the most important part: the comment!'));
}

/* captcha works this way:
	we generate an aritmethic operation, say, 3+3.
	we generate a random number, the seed.
	then we store the value in a hidden field, called auth: md5(result.site_key.seed) */
if (md5(sprintf('%s%s%s', (int)$_POST['captcha'], $settings->site_key, $_POST['seed'])) !=
		$_POST['auth']) {
	$post->add_warning(_('The result of the operation is not valid! Was it too hard for you?'));
}

if (!$post->warning) {
	$comment = new Comment();
	$comment->nick = $nick;
	$comment->mail = $mail;
	$comment->ip = $ip;
	$comment->text = $text;
	$comment->url = $url;
	$comment->status = 'shown';
	/* we don't need to check whether this post exists, it's already done in post.php */
	$comment->post_id = $post_id;
	$comment->store();
} else {
	$form['nick'] = htmlspecialchars($nick);
	$form['mail'] = htmlspecialchars($mail);
	$form['text'] = htmlspecialchars($text);
	$form['url'] = htmlspecialchars($url);
}
