#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import os, sys, re
import httplib, urllib, json
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()

def gethtmlfile( site, page ):
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

def get_type(link):
    if "list.php?tid" in link:
        return re.findall('\d+', link)[0]
    if "typeid" in link:
        return re.findall('\d+', link)[0]
    return ""

def process_text(text, cate):
    inf_arr = find_text(u"日期.*投稿人.*评论：\d*", text)
    add_arr = find_text(u"<a.*ulink.*a>", text)
    if len(inf_arr) == len(add_arr) > 0:
        for i in range(0, len(inf_arr)):
            process_article(add_arr[i], inf_arr[i], cate)

def process_article(add, inf, cate):
    if "view.php" in add:
        address = '-1'
    else:
        address = re.findall('ac\d+', add)[0]
    title = re.findall('>.+</a>', add)[0]
    title = title[1:len(title)-4]
    title = title.encode("utf-8")
    if u'投稿人： ' in inf:
        author = 'Admin'
    else:
        author = re.findall(u'投稿人.*点击', inf)[0].split(' ')[0][4:]
    author = author.encode("utf-8")
    date = re.findall('\d+-\d+-\d+', inf)[0]
    click = int(re.findall(u'点击：\d+', inf)[0][3:])
    comment = int(re.findall(u'评论：\d+', inf)[0][3:])
    sql = "SELECT * FROM ac_article WHERE ac_article_link = '"+address+"'"
    n = cursor.execute(sql)
    if n>0:
        para = (click, comment, cate, address)
        sql = "UPDATE ac_article SET ac_article_view = %s, ac_article_comment = %s, ac_article_category = %s WHERE ac_article_link = %s"
    else:
        print address[2:]
        para = (cate, title, address, address[2:], author, date, click, comment)
        sql = "INSERT INTO ac_article(ac_article_category, ac_article_name, ac_article_link, ac_article_number, ac_author_name, ac_article_date, ac_article_view, ac_article_comment, ac_article_created) VALUE(%s, %s,%s, %s, %s, %s, %s, %s, NOW()) "
    cursor.execute(sql, para)
    conn.commit()

def main():
    end = 120
    if len(sys.argv)==2:
        sql = "SELECT COUNT(*) AS count FROM ac_article WHERE ac_article_category = %s"%sys.argv[1]
        cursor.execute(sql)
        row = cursor.fetchone()
        end = row[0]/15
        if end == 0:
            return 
        else:
            end += 10

    category = ('1', '8', '9', '10', '13', '14')
    for cate in category:
        link = '/plus/list.php?typeid='+cate+'&PageNo='
        for i in range(1, end):
            retfile = ac_core.gethtmlfile( 'www.acfun.tv', link+str(i) )
            process_text(retfile.decode('GB18030'), cate)

    cursor.close()
    conn.close()

    print "\ndone"

if __name__ == "__main__":
    main()
