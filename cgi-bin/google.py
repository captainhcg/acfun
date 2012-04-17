#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging

import sys, os
import re
import urllib
import urllib2
import cookielib
from cookielib import CookieJar

class GoogleLogin:
    def __init__(self, email='hcgspace@gmail.com', password='861030', cookiejar=None):
        self.cj = cookielib.LWPCookieJar()
        # Set up our opener
        self.opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(self.cj))
        self.opener.addheaders = [('User-agent', 'Mozilla/5.0 (Windows NT 5.1; rv:8.0) Gecko/20100101 Firefox/8.0')]
        # urllib2.install_opener(self.opener)
        
        # Define URLs
        self.loing_page_url = 'https://accounts.google.com/ServiceLogin'
        self.authenticate_url = 'https://accounts.google.com/ServiceLoginAuth' 
        self.continuepage = "http://www.google.com"
        
        # Load sign in page
        login_page_contents = self.opener.open(self.loing_page_url).read()

        # Find GALX value
        galx_match_obj = re.search(r'name="GALX"\s*value="([^"]+)"', login_page_contents, re.IGNORECASE)
        dsh_match_obj = re.search(r'id="dsh"\s*value="([^"]+)"', login_page_contents, re.IGNORECASE)
       
        galx_value = galx_match_obj.group(1) if galx_match_obj.group(1) is not None else ''
        dsh_value = dsh_match_obj.group(1) if dsh_match_obj.group(1) is not None else '' 

        # Set up login credentials
        login_params = urllib.urlencode( { 
            'Email' : email,
            'Passwd' : password,
            'GALX': galx_value,
            'dsh': dsh_value
        })

        # Login
        f = self.opener.open(self.authenticate_url, login_params)

        # Open GV home page
        gv_home_page_contents = self.opener.open(self.continuepage).read()

        # Fine _rnr_se value
        key = re.search(email, gv_home_page_contents)
        
        if not key:
            print "fail to log in"
            self.logged_in = False
        else:
            print "succecss to log in"
            self.logged_in = True
            self.user = key.group(0)

    def who(self):
        print self.user

    def gethtmlfile(self, link, params = None):
        if self.opener == 0:
            return 0
        print self.opener.open(link, params).read()
    user = 0    
    opener = 0

if __name__ == "__main__":
    google = GoogleLogin()
#google = GoogleLogin("captainhcg@gmail.com", "19861030", "http://www.google.com") 
