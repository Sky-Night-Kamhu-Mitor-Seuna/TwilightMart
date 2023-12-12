#!/bin/bash
chmod 775 ./cache
chmod 775 ./templates_c
chmod 775 ./assets/uploads -R
rm ./templates_c/* -rf
rm ./cache/* -rf
touch ./templates_c/test.log
touch ./cache/test.log
chown $USER:apache * -R
restorecon -Rv .
sudo semanage fcontext -a -t httpd_sys_rw_content_t "./assets/uploads(/.*)?"
sudo restorecon -RvF ./assets/uploads