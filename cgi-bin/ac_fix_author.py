#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import os
import httplib, urllib
import sys, re
from xml.dom.minidom import parseString
from HTMLParser import HTMLParser
import datetime
import ac_core

d = datetime.datetime.now().weekday()
conn = ac_core.connect()
cursor = conn.cursor()

def fix_article(text, id):
    try:
        t = re.findall('mid=\d+.+</a>',text)[0]
    except:
        print "zhuang"
        return
    mid = re.findall('=\d+', t)[0][1:]
    name = re.findall('>.*</a>', t)[0].decode("GBK")
    name = name[1:-4]
    sql = "UPDATE ac_article SET ac_author_name = %s, ac_author_realid = %s WHERE ac_article_id = %s"
    cursor.execute(sql, (name, mid, id))
    conn.commit()

def main():
    sql = "SELECT ac_article_id, ac_article_link FROM `ac_article` WHERE ac_author_name = '' and ac_author_id = 0"
    n = cursor.execute(sql)
    articles_to_process = cursor.fetchall()
    for ac in articles_to_process:
        print ac[1]
        retfile = ac_core.gethtmlfile( 'www.acfun.tv', '/v/'+ac[1]+'/' )
        fix_article(retfile, ac[0])
    cursor.close()
    conn.close()

    print "\ndone"

if __name__ == "__main__":
    main()

