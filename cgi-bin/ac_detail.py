#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import os
import httplib, urllib
import sys
from xml.dom.minidom import parseString
from HTMLParser import HTMLParser
import datetime
import ac_core

d = datetime.datetime.now().weekday()
conn = ac_core.connect()
cursor = conn.cursor()

def getText(xml, tag):
    try:
        data = xml.getElementsByTagName(tag)[0].firstChild.data
        return data.encode('utf-8')
    except:
        return ''

def mark_removed(article_number):
    sql = "UPDATE ac_article "\
        "SET ac_article_die = ( ac_article_die + 1 ) "\
        "WHERE ac_article_number = " + str(article_number)
    cursor.execute(sql)
    conn.commit()
    print str(article_number)+" may be removed"

def update_article(article_number, inf):
    if len(inf[6])>0:
        keywords_arr = inf[6].lower().split(' ')
        sql = "SELECT ac_article_id "\
            "FROM ac_article "\
            "WHERE ac_article_number = "+str(article_number)
        cursor.execute(sql)
        ac_article_id = cursor.fetchone()[0]
        for keyword in keywords_arr:
            if keyword == '':
                continue
            sql = "SELECT ac_tag_id "\
                "FROM ac_tag "\
                "WHERE ac_article_id = %s AND ac_tag_name = %s"
            n = cursor.execute(sql, (ac_article_id, keyword))
            if n == 0:
                sql = "INSERT INTO ac_tag(ac_article_id, ac_tag_name, ac_tag_created) VALUE(%s, %s, NOW())"
                cursor.execute(sql, (ac_article_id, keyword))
                conn.commit()
    sql = "UPDATE ac_article SET "\
        "ac_article_name = %s, ac_article_category = %s, ac_author_realid = %s,"\
        "ac_article_description = %s, ac_article_view = %s, ac_article_stow = %s "\
        "where ac_article_number = "+str(article_number)
    cursor.execute(sql, (inf[0], inf[1], inf[2], inf[3], inf[4], inf[5]))
    conn.commit()

def main():
    # get the articles that ac_acticle_number % 7 == 0 
    # so that we are able to process only 1/7 articles every day, and 7/7 every week
    sql = "SELECT ac_article_number "\
        "FROM `ac_article` "\
        "ORDER BY ac_article_date DESC LIMIT 3000 "
    n = cursor.execute(sql)
    articles_to_process = cursor.fetchall()
    link = '/api/?type=xml&current=yes&charset=utf8&id='
    i = 0
    for article_to_process in articles_to_process:
        i+=1
        retfile = ac_core.gethtmlfile( 'www.acfun.tv', link+str(article_to_process[0]) )
        if retfile == "ERROR":
            # this aricle may be removed
            mark_removed(article_to_process[0])
        else:
            try:
                info = parseString(retfile)
            except:
                print str(article_to_process[0])+" xml broken"
                continue
            keywords = ''
            if i<600:
                keywords = getText(info, 'keywords')
            update_article(article_to_process[0], (getText(info, 'arctitle'),
                getText(info, 'typeid'), getText(info, 'memberID'), 
                getText(info, 'description'), getText(info, 'click'), getText(info, 'stow'),
                keywords))
    cursor.close()
    conn.close()

    print "\ndone"

if __name__ == "__main__":
    main()

