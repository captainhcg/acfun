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

class miniHTMLParser( HTMLParser ):
    def gethtmlfile( self, site, page ):
        try:
            httpconn = httplib.HTTPConnection(site)
            headers = {
                'User-Agent':'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6'
            }
            httpconn.request("GET", page, headers = headers)
            resp = httpconn.getresponse()
            resppage = resp.read()
        except:
            resppage = ""
        return resppage

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
    mySpider = miniHTMLParser()
    sql = "SELECT ac_article_id, ac_article_link, ac_article_name FROM `ac_article` LIMIT 1"
    n = cursor.execute(sql)
    articles_to_process = cursor.fetchall()
    for ac in articles_to_process:
#print 'www.google.com'+ '/search?tbm=vid&q='+ac[2]
#retfile = 
        retfile = mySpider.gethtmlfile( 'www.google.com', '/search?tbm=vid&q='+ac[2].encode('utf-8') )
        print retfile
#print 
    mySpider.close()
    cursor.close()
    conn.close()

    print "\ndone"

if __name__ == "__main__":
    main()

