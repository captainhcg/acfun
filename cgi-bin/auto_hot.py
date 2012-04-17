#!/usr/bin/env python2.7
# coding=utf-8
import sys, os, cgi
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()

def main():
    date = 7
    title = u'本周热门'
    type = 'week'
    type_arr = {'week':7, 'month':30, 'year': 365, 'all': 3650}
    title_arr = {'week':u'本周热门', 'month':u'月度热门', 'year': u'年度热门', 'all': u'坟'}

    out_str = ''
    if(len(sys.argv) == 2):
        if sys.argv[1] in type_arr:
            date = type_arr[sys.argv[1]]
            title = title_arr[sys.argv[1]]
            type = sys.argv[1]

    out_str += "<div class='hot_header'></div>"

    sql = "SELECT ac_category_name, ac_category_tid, ac_category_address FROM `ac_category` ORDER BY `ac_category_tid`"

    # 结果集为 0：名称； 1： id；2：地址
    cursor.execute(sql)
    results = cursor.fetchall()
    cate_arr = {}
    for result in results:
        cate_arr[result[1]] = result

    index = 0
    articles_arr = {}
    for category in cate_arr:
        sql = """SELECT ac_article_id, ac_article_name, ac_article_link, ac_article_view
                FROM `ac_article`
                WHERE `ac_article_category` = %s AND `ac_article_date` >= ( NOW() - INTERVAL %s DAY )
                ORDER BY `ac_article_view` DESC
                LIMIT 15"""
        cursor.execute(sql, (cate_arr[category][1], date))
        results = cursor.fetchall()
        stat = []
        for result in results:
            stat.append(result)
        articles_arr[cate_arr[category][1]] = stat

    i = 0;
    for category in cate_arr:
        margin = ''
        if i%3 == 0:
            out_str += "<div style='width: 100%'>";
        elif i%3 == 2:
            margin = 'style="margin-right: 0 !important"'

        cate = ''
        if i == 0:
            cate = 'album'
        elif i == 1:
            cate = 'music'
        elif i == 2:
            cate = 'games'
        elif i == 4:
            cate = 'pencil'
        elif i == 3:
            cate = 'youtube'
        elif i == 5:
            cate = 'movie'
        icon = "<img src='images/metro/"+cate+".png' class='cate_section_icon' />"
        out_str += """<div class='cate_section' """+margin+">"+icon+"""
                <table>
                    <tr>
                        <th colspan='2' class='cate_section_title'><a href='http://www.acfun.tv/"""+cate_arr[category][2]+"""' target='_blank'>"""+cate_arr[category][0]+"""</a></th>
                    </tr>"""
        for article in articles_arr[category]:
            ac_article_name = article[1];
            ac_article_shortname = article[1]
            if len(article[1])> 15:
                ac_article_shortname = article[1][0:15]
            out_str += """<tr>
                    <td class='art_title'>
                        <a href='http://www.acfun.tv/v/"""+article[2]+"""/' target='_blank' title='"""+ cgi.escape(ac_article_name)+"""'>"""+ cgi.escape(ac_article_shortname)+"""</a>
                    </td>
                    <td class='art_count'>"""+str(article[3])+"""</td>
                </tr>"""
        out_str += "</table></div>";
        if i%3 == 2:
            out_str += "</div>"
        i+=1

        ac_core.cache("hot_"+type, out_str);

        try:
            cursor.close()
            conn.close()
        except:
            pass

if __name__ == "__main__":
    main()

