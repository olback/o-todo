#!/usr/bin/env python

import json

with open("info.json", "r") as info:
    data = json.load(info)

print 'Old version: ', data['version']
data['version'] = data['version'] + 0.01
print 'New version: ', data['version']

with open("info.json", "w") as info:
    json.dump(data, info, indent=4)

info.close()
