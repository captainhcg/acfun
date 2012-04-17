#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging
import sys, os, cgi
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()
out_str = ''

def main():
    date = 7
    title = u'周最佳UP主'
    type = 'week'
    type_arr = {'week':7, 'month':30, 'year': 365, 'all': 3650}
    title_arr = {'week':u'周最佳UP主', 'month':u'月度UP主', 'year': u'年度UP主', 'all': u'基友堂'}
    out_str = ''
    if(len(sys.argv) == 2):
        if sys.argv[1] in type_arr:
            date = type_arr[sys.argv[1]]
            title = title_arr[sys.argv[1]]
            type = sys.argv[1]

    sql = """SELECT `ac_author_name` AS name, `ac_author_id` AS id, SUM( `ac_article_view` ) AS view, COUNT( * ) AS post
            FROM `ac_article`
            WHERE `ac_article_date` >= ( NOW() - INTERVAL """ + str(date) + """ DAY )
            GROUP BY `ac_author_id`
            ORDER BY view DESC
            LIMIT 21 """;

    # 结果集为 0：姓名； 1： id；2：所有投稿的总点击量；3：投稿总数
    cursor.execute(sql)
    results = cursor.fetchall()
    data_arr = []
    for result in results:
        data_arr.append(result)

    # 过去的排名
    oldnum_arr = {}
    # 除了名人堂不用比较趋势，其他三项都需要比较，所以要再取一次以前的（昨天的）数据
    if date != 3650:
        sql = """SELECT `ac_author_name` AS name, `ac_author_id` AS id, SUM( `ac_article_view` ) AS view, COUNT( * ) AS post
            FROM `ac_article`
            WHERE `ac_article_date` <= ( NOW() - INTERVAL 1 DAY )
                  AND `ac_article_date` >= (NOW() - INTERVAL """ + str(date + 1) + """ DAY)
            GROUP BY `ac_author_id`
            ORDER BY view DESC
            LIMIT 20 """;
        cursor.execute(sql)
        results = cursor.fetchall()
        num = 0
        for result in results:
            oldnum_arr[result[1]] = num
            num = num + 1

    out_str += '<div class="main">'
    for i in range(0, 21):
        trend = "";
        if date != 3650:
            trend = "<img src='images/metro/"
            if data_arr[i][1] not in oldnum_arr:
                trend += "arrow_up"
            else:
                if i < oldnum_arr[data_arr[i][1]]:
                    trend += "arrow_up"
                elif i > oldnum_arr[data_arr[i][1]]:
                    trend += "arrow_down"
                else:
                    trend += "minus"
            trend += ".png' class='trend_icon'>"
        if i < 3:
            out_str += '<div class="up_top">';
            out_str += '<div class="up_top_info">'+trend+'<table width="100%"><tr><td class="up_top_titile"><a href="http://wiki.acfun.tv/index.php/'+cgi.escape(data_arr[i][0])+'" target="_blank"><span style="font-size: ' + str(26 - 2 * i) + 'px">' + cgi.escape(data_arr[i][0]) + """</span></a></td></tr>
                    <tr><td class="up_top_stat">View:&nbsp;&nbsp;""" + str(data_arr[i][2]) + """</td></tr>
                    <tr><td class="up_posts">""" + top_posts(str(data_arr[i][1]), str(date)) + '</td></tr></table></div>'
            out_str += '</div>';
        else:
            font_size = (i < 7) and (25 - i) or 18
            out_str += '<div class="up_normal">';
            out_str += '<div class="up_normal_info">'+trend+'<table width="100%"><tr><td class="up_normal_titile"><a href="http://wiki.acfun.tv/index.php/'+ cgi.escape(data_arr[i][0])+'" target="_blank"><span style="font-size:' + str(font_size) + 'px">' + cgi.escape(data_arr[i][0]) + """</span></a></td></tr>
                    <tr><td class="up_normal_stat">View:&nbsp;&nbsp; """ + str(data_arr[i][2]) + """</td></tr>
                    <tr><td class="up_posts">""" + top_posts(str(data_arr[i][1]), str(date)) + '</td></tr></table></div>'
            out_str += '</div>';

    out_str += '</div>';
    
    ac_core.cache("up_"+type, out_str)
    try:
        cursor.close()
        conn.close()
    except:
        pass 

def top_posts(author_id, date):
    sql = """SELECT `ac_article_name` AS name, `ac_article_link` AS link, `ac_article_view` AS view
        FROM `ac_article`
        WHERE `ac_article_date` >= ( NOW() - INTERVAL """+date+""" DAY )
        AND `ac_author_id` = """+author_id+"""
        ORDER BY view DESC
        LIMIT 3 """;
    cursor.execute(sql)
    results = cursor.fetchall()
    str = ''
    for result in results:
        str += "<li><a href='http://www.acfun.tv/v/"+result[1]+"/' target='_blank'>"+result[0]+"</a></li>";
    return str

if __name__ == "__main__":
    main()
