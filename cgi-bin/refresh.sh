#!/bin/sh
/var/www/html/acfun/cgi-bin/./auto_up.py week
/var/www/html/acfun/cgi-bin/./auto_up.py month
/var/www/html/acfun/cgi-bin/./auto_up.py year
/var/www/html/acfun/cgi-bin/./auto_up.py all

/var/www/html/acfun/cgi-bin/./auto_hot.py week
/var/www/html/acfun/cgi-bin/./auto_hot.py month
/var/www/html/acfun/cgi-bin/./auto_hot.py year
/var/www/html/acfun/cgi-bin/./auto_hot.py all

/var/www/html/acfun/cgi-bin/./auto_bookmark.py week
/var/www/html/acfun/cgi-bin/./auto_bookmark.py month
/var/www/html/acfun/cgi-bin/./auto_bookmark.py year
/var/www/html/acfun/cgi-bin/./auto_bookmark.py all

/var/www/html/acfun/cgi-bin/./auto_today.py
/var/www/html/acfun/cgi-bin/./auto_tag_cloud.py

/etc/init.d/memcached restart
