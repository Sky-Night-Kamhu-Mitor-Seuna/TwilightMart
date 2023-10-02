#!/bin/bash
sudo chown $USER:apache * -R
chmod 775 ./cache
chmod 775 ./templates_c
rm ./templates_c/* -rf
rm ./cache/* -rf
touch ./templates_c/test.log
touch ./cache/test.log
sudo chown $USER:apache * -R
restorecon -Rv .
