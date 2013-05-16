escriure installation guide
===========================

Prerequisites
-------------

* You need a http server (we'll talk about Apache and Lighttpd here) and PHP.
* MySQL/MariaDB and pdo_mysql/pdo_sqlite PHP extension, depending on what
  database system you want to use.
* gettext PHP extension if you want to use a language other than English.
* `git` for downloading the code.
* One or two (sub)domains, depending on whether you want to separate the
  static content.

Installation
------------

* Go to the folder where you want to install _escriure_ and clone the repo:

	git clone git://github.com/wodim/fearqdb.git

* Set up the database:
  * **For MySQL**, create a new user and database. Then, install the
    `sql/escriure.mysql` file.
  * **For SQLite**, create a folder called `database` and make it readable and
    writable by your http server.
* Configure your web server. First, create a new domain pointing to the _escriure_
  installation, and then another one for your statics domain (both of them pointing
  to the same folder). Then, create the necessary rewrite rules. All requests
  must be handled by `index.php`. For example:
  * **For Apache**, inside your `.htaccess`:

	RewriteEngine On
	RewriteRule ^ index.php

  * **For Lighttpd**, inside your host(s) in `lighttpd.conf`:

	url.rewrite-once = ( "" => "/index.php" )

* Create the `templates/compiled` folder and make it writable by the http server.
  Haanga stores its compiled templates there. You don't need to care at all of it.
* Edit the `config.php` file to suit your needs.
* Now, open your database editor (phpMyAdmin, Adminer, command-line tools...) to
  create the virtual hosts. To do so, insert a new row in the `sites` table. This
  is the meaning of the fields:
  * **id**: AUTO_INCREMENT, you don't need to edit it.
  * **domain**: your domain. Example: `blog.example.com`
  * **statics_only**: whether this domain will serve static content only. You need
    to set it as `1` for your static domains only.
  * **lang**: the language your blog will use. Example: `es`
  * **locale**: the locale your blog will use. Needed only if you are going to use
    a language other than English via gettext. Example: `es_ES.UTF-8`
  * **collate**: collate for your database. Currently not used. Example:
    `utf8_spanish_ci`
  * **analytics_enabled**: whether you will use the bundled Google Analytics
    support. Example: `1`
  * **analytics_code**: your Google Analytics code. Example: `UA-1488-3`
  * **url**: your base URL, with a trailing `/`. Example: `http://blog.example.com/`
  * **statics_url**: your statics base URL, with a trailing `/`. Can be the same as
    `url` if you're not going to use a dedicated domain for static content.
    Example: `http://static.blog.example.com/`
  * **theme**: the theme this blog is going to use. Each subfolder in `templates`
    is a valid option. Example: `nectar`
  * **db**: the database name. It is the way different blogs are separated inside
    the database itself, so use a unique name for each virtual host. Example: `blog`
  * **title**: the title of your blog. It will appear on each page. Example: `My blog`
  * **page_size**: the number of posts that will appear on each page. Example: `5`
  * **robots**: the `robots.txt` file is generated automatically by _escriure_.
    You can set here whether you want to allow bots to crawl your blog. Valid
    options are `allow` or `disallow`
  * **mail**: a contact mail, used by some themes and as the `From` headers for
    mails sent by _escriure_.
  * **site_key**: a random key used by several functions such as the comment antispam
    system. Create a random password and you're set.
  * **meta_json**: some meta information in JSON format about your blog, used by
    some themes. Not required.
  * **admin_mail**: your own private mail, used by the new comment notification
    system. Not to be disclosed.
* If you want a domain for statics only, create a new row in the `sites` table,
  but with the `domain`, `statics_only` and `theme` fields only.

Aaaand it should work. To insert a post, create a new row in the `posts` table:

* **id**: AUTO_INCREMENT, you don't need to edit it.
* **permaid**: the permaid of the post. This is, the part after `http://blog.example.com/`.
  Example: `this-is-a-new-post`
* **nick**: the nick or name of the person who published this post. Appears on different
  places depending on the theme and in the RSS feed.
* **timestamp**: the UNIX timestamp of the time this post was published. Posts are
  **not** ordered by the timestamp, but by the `id` field.
* **title**: self-explanatory.
* **text_html**: if you are going to write your post in HTML, write it here.
* **text_markdown**: if you are going to write your post in Markdown, write it here.
* **text_type**: whether this post was written in HTML or Markdown. If you have
  written it in HTML, use `html` here. Otherwise, use `markdown`. If you use Markdown,
  once the post is displayed once, it is compiled to HTML, stored in the
  `text_html` field, and the `text_type` field is set to `html`. This is, subsequent
  requests don't require the Markdown code to be compiled. You can edit the Markdown code
  further and then set the `text_type` back to `markdown` for it to be recompiled.
* **tags**: may be used by some themes and by the meta-tags HTML tag.
* **db**: the database this post is stored on. This defines the blog inside the post
  will appear. It needs to be the same you set in the `sites` table before.
* **status**: any thing other than `published` will hide the post.
* **comment_count**: the amount of comments this post has. `0` by default.
* **comment_status**: `open` to allow comments, `closed` to close them but show the
  comments that were already published, `hidden` to completely hide the previous
  comments and the comment field.
* **twitter**: if set to `1`, this entry will appear in the `http://blog.example.com/rss/twitter`
  RSS feed. Useful if you use dlvr.it and don't want all of your posts to be published.