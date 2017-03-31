# Visit Checks

Description here...

### Read Further

https://mwop.net/blog/2015-12-14-secure-phar-automation.html
https://moquet.net/blog/distributing-php-cli/

https://www.thomas-krenn.com/de/wiki/Md5sum_und_sha1sum_zum_%C3%9Cberpr%C3%BCfen_von_Dateidownloads_verwenden#MD5.2FSHA1-Summe_erzeugen

https://rogerdudler.github.io/git-guide/index.de.html

http://stackoverflow.com/questions/11729069/get-a-list-of-files-and-filenames-without-an-extention-using-php

### On Windows in Root Project Dir:

# \OpenSSL-Win64\bin\openssl.exe genrsa -des3 -out c:\www\zabbix-checks\phar-private.pem 4096
# copy phar-private.pem phar-private.pem.passphrase-protected
# \OpenSSL-Win64\bin\openssl.exe rsa -in phar-private.pem -out phar-private-nopassphrase.pem
# copy phar-private-nopassphrase.pem phar-private.pem
# del phar-private.pem.passphrase-protected
# mkdir .travis
# move phar-private.pem .travis/
# php -d phar.readonly=0 box.phar build -vv

