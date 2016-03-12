PALOR
=====

PALOR is a software which can be used to analyze activity data collected by an activity tracker.

PALOR uses a web frontend to sync and readout your activity tracker.

Please make sure that you read the license before you use this software.
Any use of the software or following the installation instructions is entirely at your own risk.


Installation (Linux)
--------------------

Example installation on a Banana Pi

  * Install Python 2, Apache2 and PHP:

    `sudo apt-get install python2.7 apache2 php5 python-setuptools`

  * You have to install packages needed by loophole:

    [protobuf](https://pypi.python.org/pypi/protobuf/3.0.0b2)

    [pyusb](https://github.com/walac/pyusb)

    See more details here: https://github.com/rsc-dev/loophole

  * Copy or clone the whole project to the web server base directory.
    In our example this is `/var/www/html/palor/`.

  * Use chown to set the user and group for the project to the user used by apache (e.g www-data):

   `sudo chown -R www-data:www-data /var/www/html/palor/`

  * Make sure that `python/dump_devices.py` is executable:

    `sudo chmod +x /var/www/html/palor/python/dump_devices.py`

  * Modify or create your virtual host configuration file
    (e.g. `/etc/apache2/sites-available/000-default.conf`) and add the
    following lines:

    `<Directory /var/www/html/palor/python>`
    
                `Options +ExecCGI`
                
                `AddHandler cgi-script .py`
                
    `</Directory>`

    This will allow to execute python scripts from the webserver instance.
    Be careful if your webserver is accessible to the public.
    In this case you maybe should dump the devices manually.

  * Make sure that USB devices are accessible without being root.
    Create a file in `/etc/udev/rules.d/90-palor-usb.rules` and insert following line:

    `ACTION=="add", SUBSYSTEMS=="usb", ATTR{idVendor}=="0da4" MODE="660", GROUP="usbusers"`

    Execute following commands in a shell:

    `sudo groupadd usbusers` and `sudo adduser www-data usbusers`

  * Force the udev system to see your changes by executing

    `sudo udevadm control --reload` and `sudo udevadm trigger`

  * Restart the apache server:

    `sudo service apache2 restart`

  * Use the web frontend to access your activity tracker data.

Device compatibility list (tested)
----------------------------------

  * LOOP


Known issues
------------

  * If your device memory is full you have to clear it by using the
    original software by the activity tracker manufacturer.
  * Sometimes the connection is broken during the sync.
    At the moment the only workaround is to try it again.

