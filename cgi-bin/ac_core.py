import httplib
import mysql.connector as mdb
import memcache

def get_memc():
    return memcache.Client(['127.0.0.1:11211'], debug=1)

def connect():
    return mdb.connect(host="127.0.0.1", user="root", passwd="qwert03", db="acfun", charset="utf8", buffered=True)

def cache( filename, out_str, encode = True):
    try:
        with open("/var/www/acfun/cache/"+filename+".cache", "w") as output:
            if encode:
                output.writelines(out_str.encode('utf-8'))
            else:
                output.writelines(out_str)
            output.close()
    except:
        pass

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
