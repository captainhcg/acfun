#!/usr/bin/env python
# coding=utf-8
import sys
import os
import cgi
import MySQLdb as mdb

conn = mdb.connect(host="127.0.0.1", user="root", passwd="861030", db="acfun", charset="utf8")
cursor = conn.cursor()
out_str = ''

def main():
    up_id = '0'

    out_str = ''
    if(len(sys.argv) == 2):
        up_id = sys.argv[1]

    out_str += "<div class='classic_up_title'>你可能错过的。。。</div>"

    sql = """SELECT ac_article_id, ac_article_name, ac_article_link
            FROM `ac_article`
            WHERE ac_author_id = """+up_id+""" AND `ac_article_date` < ( NOW() - INTERVAL 365 DAY ) AND ac_article_view > 10000 
            ORDER BY `ac_article_view` DESC
            LIMIT 8"""

    cursor.execute(sql)
    results = cursor.fetchall()
    classic_arr = ()
    out_str += "<ul>"
    for result in results:
        out_str += "<li><a href='http://www.acfun.tv/v/"+result[2].encode('utf-8')+"' target='_blank' >"
        out_str += result[1].encode('utf-8')
        out_str += "</a></li>"
    out_str += "</ul>"
    out_str += "</div>"

    try:
        with open("/var/www/acfun/view/classic_up_"+up_id+".cache", "w") as output:
            output.writelines(out_str)
            output.close()
    except:
        pass
    try:
        cursor.close()
        conn.close()
    except:
        pass

if __name__ == "__main__":
    main()

