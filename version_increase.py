#!/usr/bin/env python

import json, os

class bcolors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

status = os.popen('git status --porcelain').read()

if status == '' or status == '\n' or status == None:
    exit()

print bcolors.WARNING + 'Changes detected, updating version.' + bcolors.ENDC

with open("info.json", "r") as info:
    data = json.load(info)

print 'Old version: ' + bcolors.FAIL, data['version'], bcolors.ENDC
data['version'] = data['version'] + 0.01
print 'New version: ' + bcolors.OKGREEN, data['version'], bcolors.ENDC

with open("info.json", "w") as info:
    json.dump(data, info, indent=4)

info.close()
