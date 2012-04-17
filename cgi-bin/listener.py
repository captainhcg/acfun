#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import sys, os, cgi, imp
import cgitb
cgitb.enable()

print "Content-type: text/html"
print

REQUEST = cgi.FieldStorage()
if not REQUEST['type'].value:
    sys.exit()

if REQUEST['type'].value == "tag":
    import tag_cloud
    print tag_cloud.get_cloud(REQUEST['tag'].value, REQUEST['date'].value)

elif REQUEST['type'].value == "author":
    print "nothing"

elif REQUEST['type'].value == "post":
    # generate wiki of post(s)
    keywords = REQUEST['keywords'].value
    fp, pathname, description = imp.find_module("load_post")
    load_post = imp.load_module("load_post", fp, pathname, description )
    if "author" in REQUEST:
        print load_post.draw_post( keywords, REQUEST['author'].value.strip() )
    else:	
        print load_post.draw_post( keywords )

elif REQUEST['type'].value == "search":
    import search_post
    print search_post.get_posts(REQUEST['keywords'].value)
