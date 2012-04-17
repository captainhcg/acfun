#!/usr/bin/env python2.7
# coding=utf-8
import sys, os, cgi
import ac_core
import base64

conn = ac_core.connect()
cursor = conn.cursor()

def get_posts(keyword):
    memc = ac_core.get_memc()
    posts = memc.get("search_posts_"+base64.standard_b64encode(keyword))
    if posts:
        return print_posts(posts)
    
    posts = find_posts(keyword)
    if not posts:
        return 'NO RELATED POSTS!'

    memc.set("search_posts_"+base64.standard_b64encode(keyword), posts, 3600)
    return print_posts(posts)

def print_posts(posts):   
    po = ""
    for post in posts:
        po += '<li class="sidebar_section_today_item">'\
                '<a href="http://www.acfun.tv/v/'+post[0].encode('utf-8')+'/" target="_blank">'+post[1].encode('utf-8')+'</a>'\
            '</li>'
    return po

def find_posts(keyword = ''):
    if keyword == '':
        return None
    keywords = keyword.split(' ')
    condition = ""
    if len(keywords) == 1:
        if len(keyword)>1:
            condition = "ac_article_name LIKE '%%%s%%' OR ac_article_description LIKE '%%%s%%'"%(keyword, keyword)
    else:
        if len(keywords[0])>1:
            condition = "(ac_article_name LIKE '%%%s%%' OR ac_article_description LIKE '%%%s%%')"%(keywords[0], keywords[0])
        for key in keywords[1:]:
            if len(key)>1:
                condition += " AND (ac_article_name LIKE '%%%s%%' OR ac_article_description LIKE '%%%s%%')"%(key, key)
    if condition=="":
        return None 
    sql = 'SELECT ac_article_link, ac_article_name FROM ac_article '\
        'WHERE '+condition+' '\
        'ORDER BY ac_article.ac_article_date DESC LIMIT 10'
    cursor.execute(sql)
    results = cursor.fetchall()
    return results;


