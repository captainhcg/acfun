#!/usr/bin/env python2.7
# -*- coding: UTF-8 -*-
# enable debugging

import sys, os, cgi
import ac_core
import tag_cloud

conn = ac_core.connect()
cursor = conn.cursor()
out_str = ''

ac_core.cache('tag_cloud_week', tag_cloud.get_cloud('', 7), False)
ac_core.cache('tag_cloud_month', tag_cloud.get_cloud('', 30), False)
ac_core.cache('tag_cloud_year', tag_cloud.get_cloud('',365), False)
ac_core.cache('tag_cloud_all', tag_cloud.get_cloud('', 3650), False)

try:
    cursor.close()
    conn.close()
except:
    pass
