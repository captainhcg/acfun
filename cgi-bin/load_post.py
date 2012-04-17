import ac_core
# coding=utf-8
conn = ac_core.connect()
cursor = conn.cursor()

def draw_post( k, a='' ):
    if( k.strip == ''):
        return "at least one keyword must be assigned"

    keywords = k.split(' ')
    words_cond = "ac_article_name LIKE %s"
    for index, word in enumerate(keywords):
        keywords[index] = '%'+word+'%'
        if index != 0:
            words_cond += " AND ac_article_name LIKE %s"

    author_cond = '1'
    if len(a)>0:
        author_cond = "ac_author_name LIKE %s"
        keywords.append(a)
    num_words = len(keywords)

    sql = "SELECT a.ac_article_name, a.ac_article_link, a.ac_article_number, a.ac_author_name, a.ac_article_date "\
        "FROM ac_article AS a "\
        "WHERE ("""+words_cond+""") AND """+author_cond+" ORDER BY ac_article_date ASC"
    n = cursor.execute(sql, keywords);
    if n == 0:
        return "no result"
    if n > 100:
        return "too many result, please use more accurate keywords"
    articles = cursor.fetchall()
    post = draw_sortable_ul(articles)       
    post += "<div class='wiki_nav_buttons'><input type='button' value='下一步' class='button wiki_button_next' onclick='process_posts()'></div>"
    return post+load_js()

def draw_sortable_ul(articles):
    ul = "<ul id='wiki_sortable' class='wiki_post_ul'>"
    for article in articles:
        ul += draw_sortable_li(article)
    ul += "</ul>"
    return ul
        
def draw_sortable_li(article):
    li = "<li id='wiki_post_"+str(article[2])+"' number="+str(article[2])+" class='wiki_post_li ui-state-default'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>"
    li +="<span class='wiki_post_title' title='"+article[0].encode('utf-8')+"'>"
    if len(article[0])>24:
        li += article[0][:23].encode('utf-8')+"...</span>"
    else:
        li += article[0].encode('utf-8')+"</span>"
    li += "<span class='wiki_post_button ui-icon ui-icon-closethick' style='float:right' onclick=\"remove_post('"+str(article[2])+"')\" title='删除'></span>"
    li += "<span class='wiki_post_button ui-icon ui-icon-play' style='float:right' onclick=\"open_post('"+str(article[2])+"')\" title='查看'></span>"
    li += "<span class='wiki_post_date'>"+str(article[4])+"</span>"
    li += "<span class='wiki_post_author'>"+article[3].encode('utf-8')+"</span>"
    return li+'</li>'

def load_js():
    js = """
    <script>
    $(function() {
        $( "#wiki_sortable" ).sortable();
        $( "#wiki_sortable" ).disableSelection();
    });
    </script>""" 
    return js       
        
