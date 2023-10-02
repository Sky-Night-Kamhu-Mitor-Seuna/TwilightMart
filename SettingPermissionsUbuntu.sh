#!/bin/bash
sudo chown $USER:www-data * -R
chmod 775 ./cache
chmod 775 ./templates_c
rm ./templates_c/* -rf
rm ./cache/* -rf
touch ./templates_c/test.log
touch ./cache/test.log
sudo chown $USER:www-data * -R
# restorecon -Rv .
