#!/usr/bin/env python2.7
# coding=utf-8
import sys, os, cgi
import ac_core

conn = ac_core.connect()
cursor = conn.cursor()

def get_cloud(keyword, date = 7):
    memc = ac_core.get_memc()
    tags = memc.get("tag_cloud_"+keyword)
    posts = memc.get("posts_cloud_"+keyword)
    if tags and posts:
        return print_cloud(tags, keywordi, date)
    
    tags = get_tags(keyword, date)
    posts = get_posts(keyword, date)
    if not tags:
        return 'NO RELATED TAGS!'

    memc.set("post_cloud_"+keyword, posts, 3600)
    memc.set("tag_cloud_"+keyword, tags, 3600)
    return print_cloud(tags, posts, date)

def print_cloud(tags, posts, date):   
    output = """
        <canvas width="240px" height="200px" id="tag_cloud_canvas">
            <p>Anything in here will be replaced on browsers that support the canvas element</p>
        </canvas>
        <div id="tags">
            <ul>
                %s
            </ul>
        </div>
        <div style="width: 100%%">
            %s
        </div>
        <script type="text/javascript">
            if(!$('#tag_cloud_canvas').tagcanvas({
                textColour: '#FFF',
                weight: true,
                textFont: "'Microsoft Yahei', Arial, sans-serif",
                outlineColour: '#FFF',
                outlineThickness: 1,
                reverse: true,
                depth: 0.95,
                minBrightness: 0.2,
                textHeight: 16,
                maxSpeed: 0.05
                },'tags')) {
                    // something went wrong, hide the canvas container
                    $('#ac_tag_cloud').hide();
                }
        </script>"""
    li = ''
    for tag in tags:
        li += "<li><a href='javascript:void(0)' onclick='load_tag(\""+tag[0].encode('utf-8')+"\", "+str(date)+")'>"+tag[0].encode('utf-8')+"</a><li>\n"
    po = ''
    for post in posts:
        po += '<li class="sidebar_section_today_item">'\
                '<a href="http://www.acfun.tv/v/'+post[0].encode('utf-8')+'/" target="_blank">'+post[1].encode('utf-8')+'</a>'\
            '</li>'
    return output%(li, po)

def get_posts(keyword = '', date = 7):
    if keyword == '':
        return ''
    sql = 'SELECT ac_article_link, ac_article_name, ac_author_name, ac_article_view FROM ac_article '\
        'INNER JOIN ac_tag ON ac_article.ac_article_id = ac_tag.ac_article_id '\
        'WHERE ac_tag_name = "'+keyword+'" '\
        'AND `ac_article_date` >= ( NOW() - INTERVAL '+str(date)+' DAY ) '\
        'ORDER BY ac_article.ac_article_view DESC LIMIT 3'
    cursor.execute(sql)
    results = cursor.fetchall()
    return results;

def get_tags(keyword = '', date = 7 ):
    if keyword == '':
        sql = "SELECT AT.`ac_tag_name`, COUNT(*) AS total "\
            "FROM ac_tag AS AT inner JOIN ac_article AS AA "\
            "WHERE AT.ac_article_id = AA.ac_article_id AND AA. `ac_article_date` >= ( NOW() - INTERVAL "+str(date)+" DAY ) "\
            "GROUP BY AT.`ac_tag_name` ORDER BY `total` DESC LIMIT 24"
    else:
        sql = "SELECT AT2.`ac_tag_name`, COUNT(*) AS total FROM `ac_tag` as AT1 "\
            "INNER JOIN ac_article AS AA ON AT1.ac_article_id = AA.ac_article_id "\
            "INNER JOIN `ac_tag` AS AT2 "\
            "WHERE AA.`ac_article_date` >= ( NOW() - INTERVAL "+str(date)+" DAY ) "\
                "AND AT1.`ac_article_id` = AT2.`ac_article_id` "\
                "AND AT1.`ac_tag_name` = '"+keyword+"' "\
            "GROUP BY `AT2`.`ac_tag_name` "\
            "ORDER BY total DESC LIMIT 24"
    cursor.execute(sql)
    results = cursor.fetchall()
    related_tags = []
    for keyword in results:
        related_tags.append((keyword[0], keyword[1]))
    return related_tags

