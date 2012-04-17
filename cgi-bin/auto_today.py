#!/usr/bin/env python2.7
# coding=utf-8
import sys, os, time
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()
ISOTIMEFORMAT='%Y-%m-%d'

def main():
    beijing_time = time.gmtime(time.mktime(time.localtime())+28800)
    bejjing_date = time.strftime(ISOTIMEFORMAT, beijing_time)
    bejjing_monthday = time.strftime('%m-%d', beijing_time)
    out_str = ''
    sql = """
            SELECT ad.ac_date_year, ad.ac_date, COUNT(DISTINCT ac_author_id) as author,
            COUNT( * ) AS post, SUM( ac_article_view ) AS view, SUM( ac_article_comment) AS comment
            FROM ac_article AS aa
            INNER JOIN ac_date AS ad ON aa.ac_article_date = ad.ac_date
            WHERE ad.ac_date != %s AND ad.ac_date_monthday = %s
            GROUP BY ad.ac_date_year, ad.ac_date_month ORDER BY ad.ac_date DESC"""

    cursor.execute(sql, (bejjing_date, bejjing_monthday))
    results = cursor.fetchall()

    date_summary = []
    for row in results:
        date_summary.append(row)

    for stat in date_summary:
        out_str += '\t<div class="sidebar_section_subtitle">\n\t\t%s\n'%time.strftime('%Y年%m月%d日',time.strptime(str(stat[1]), ISOTIMEFORMAT))
        out_str += '\t</div>\n'
        out_str += '\t<div class="sidebar_section_today_content">\n\t\t'
        sql = "SELECT ac_article_link, ac_article_name, ac_author_name, ac_article_view FROM ac_article WHERE ac_article_date = '"+str(stat[1])+"' ORDER BY ac_article_view DESC LIMIT 3"
        cursor.execute(sql)
        results = cursor.fetchall()
        top = True
        for row in results:
            red = 'class="sidebar_section_today_item_top"'
            if top == True:
                top = False
            else:
                red = ''
            out_str += '\t\t<li class="sidebar_section_today_item">\n\t\t\t<a '+red+' href="http://www.acfun.tv/v/%s/" target="_blank">%s</a>\n\t\t</li>\n'%(row[0].encode('utf-8'), row[1].encode('utf-8'))

        out_str += '\t</div>\n'

    ac_core.cache("today", out_str, False)

    try:
        cursor.close()
        conn.close()
    except:
        pass

if __name__ == "__main__":
    main()

