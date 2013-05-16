escriure
--------

***escriure*** is a lightweight yet feature-full blog system.

**What does _escriure_ have?**

* Supports both SQLite and MySQL/MariaDB.
* Complete virtual hosting system: you can have as many blogs as you want sharing
  the same code and database installation.
* Primitive SEO:
  * robots.txt
  * sitemap.xml
  * `<link rel="canonical" />`
  * `<meta name="keywords" />`
* Bundled support for Google Analytics.
* Two separate RSS channels: one for the blog itself and another one for automatic
  sharing using services like dlvr.it that allow you to filter which posts you want
  to share.
* Drafts.
* Archive (posts list).
* Comments with an archaic antispam system. Replies to comments by the post author.
* It is possible to embed static files (such as images, PDFs, etc) in the database
  itself as BLOBs for better portability of your blog.
* Precompiled Markdown support. This is, you can write your posts in Markdown and
  _escriure_ compiles to HTML and stores them in your database so subsequent requests
  don't require compiling the Markdown code again.
* Lightweight:
  * Page generation time under 0.01 seconds (depends on your system, obviously).
  * Only two HTTP requests by default: one for the HTML document and another one for
    the CSS file (which is cached for subsequent requests).
  * Static files are in a separate domain for improved loading times.
  * Does not even set cookies.
* Complete theming system using [Haanga](http://haanga.org/).
* Several themes included by default that are easy to understand and fork.
  Some of them are even html5-only.
* Codebase is small and has been reviewed and written with an eye on security.
  This is not [a remote shell](http://www.bash.org/?949214).
* Fully translatable using gettext. Translation to Spanish included.

**What does _escriure_ lack (and will probably never have)?**

* A CMS for adding/modifying posts or for moderating comments. You can use other tools
  such as
  [phpMyAdmin](http://www.phpmyadmin.net/home_page/index.php) (MySQL/MariaDB only),
  [Adminer](http://www.adminer.org/),
  [SQLite Browser](http://sqlitebrowser.sourceforge.net/) (SQLite only), etc.
* An easy installation system. It's a pain in the ass, but you have to do it only once.
  More information is available in the `INSTALL.md` file.
* Search system. You could use Google Custom Search and even include it in your theme,
  though.
* Installation inside a folder. _escriure_ needs a dedicated domain for itself.