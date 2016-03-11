#!/usr/bin/env python

__author__      = 'Ralph Haussmann'
__copyright__   = 'Copyright (c) 2016 Ralph Haussmann'
__license__     = 'MIT'

import os, sys
sys.path.append(os.path.join("loophole", "loophole"))

from polar import Device

print "Content-type: text/html\n"

path = '../device_data'

try:
    devs = Device.list()
    if len(devs) > 0:
        print 'Following devices are connected:'
        print '<ul>'
        for i, dev in enumerate(devs):
            info = Device.get_info(dev)
            print '<li>{} ({})</li>'.format(info['product_name'], info['serial_number'])
            dev_obj = Device(dev)
            dev_obj.open()

            dev_map = dev_obj.walk(dev_obj.SEP)
            for directory in dev_map.keys():
                fixed_directory = directory.replace(dev_obj.SEP, os.sep)
                path_serial = os.path.join(path, info['serial_number'])
                full_path = os.path.abspath(os.path.join(path_serial, fixed_directory[1:]))

                if not os.path.exists(full_path):
                    os.makedirs(full_path)

                d = dev_map[directory]
                files = [e for e in d.entries if not e.name.endswith('/')]
                for file in files:
                    with open(os.path.join(full_path, file.name), 'wb') as fh:
                        data = dev_obj.read_file('{}{}'.format(directory, file.name))
                        fh.write(bytearray(data))

            dev_obj.close()
        print '</ul>'
        print 'All devices synchronized.'
    else:
        print 'No device connected'

except:
    print 'Error while synchronizing the device(s)'