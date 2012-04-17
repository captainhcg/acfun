#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import os, sys, re
import httplib, urllib, json
from HTMLParser import HTMLParser
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()

def find_text(regex, text):
    res=re.findall(regex, text)
    arr = []
    if res:
        for r in res:
            try:
                arr.append(r)
            except:
                pass
    return arr

def process_text(text, cate):
    stat_arr = find_text("class=\"previewStatistics\".*?回复", text)
    inf_arr = find_text("class=\"infomation\".*?leftIntro", text)
    if len(stat_arr) == len(inf_arr) > 0:
        for i in range(0, len(inf_arr)):
            process_article(stat_arr[i], inf_arr[i], cate)

def process_article(stat, inf, cate):
    if "view.php" in inf:
        address = '-1'
    else:
        address = re.findall('ac\d+', inf)[0]
    title = re.findall('/\'>.+</a>', inf)[0]
    title = title[3:-4]
    if '<span class="authorDate"></span>' in inf:
        author = 'Admin'
    else:
        author = re.findall("Date\">.*?</span>", inf)[0][6:-7]
    date = re.findall('\d+/\d+/\d+', inf)[0]
    stat = stat.split('</p>')
    click = re.findall('\d+',stat[0])[0]
    comment = re.findall('\d+',stat[3])[0]
    sql = "SELECT * FROM ac_article WHERE ac_article_link = '"+address+"'"
    n = cursor.execute(sql)
    if n>0:
        para = (click, comment, cate, address)
        sql = "UPDATE ac_article "\
            "SET ac_article_view = %s, ac_article_comment = %s, ac_article_category = %s "\
            "WHERE ac_article_link = %s"
    else:
        para = (cate, title, address, address[2:], author, date, click, comment)
        sql = "INSERT INTO ac_article("\
               "ac_article_category, "\
               "ac_article_name, "\
               "ac_article_link, "\
               "ac_article_number, "\
               "ac_author_name, "\
               "ac_article_date, "\
               "ac_article_view, "\
               "ac_article_comment, "\
               "ac_article_created)"\
               "VALUE(%s, %s,%s, %s, %s, %s, %s, %s, NOW()) "
    cursor.execute(sql, para)
    conn.commit()

def main():
    end = 120
    if len(sys.argv)==2:
        sql = "SELECT COUNT(*) AS count FROM ac_article WHERE ac_article_category = %s"%sys.argv[1]
        cursor.execute(sql)
        row = cursor.fetchone()
        end = row[0]/15

    link = '/zj/'
    for i in range(1, end):
        #print "checking link ", link+str(i)+'.html'
        retfile = ac_core.gethtmlfile( 'www.acfun.tv', link+str(i)+'.html' )
        process_text(retfile, 7)

    cursor.close()
    conn.close()

    print "\ndone\n"

if __name__ == "__main__":
    main()
