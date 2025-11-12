# README #
Check-in 's Faculty of Education, Chiang Mai University

## Environment ##
1. Create folder: ~/app/install
3. Create a file: ~/app/install/.htaccess
```
<IfModule mod_rewrite.c>
    Options -Indexes
</IfModule>
<Files env.conf> 
    Order Allow,Deny
    Deny from all
</Files>
```
4. Create a file: ~/app/install/env.conf
```
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=echeck
DB_USER=
DB_PASS=
MAIL_HOST=
MAIL_POST=
MAIL_USER=
MAIL_PASS=
MAIL_SMTP=
MAIL_ADMIN=
MAIL_NOREPLY=no-reply-edu@cmu.ac.th
GOOGLE_APP_ID=
GOOGLE_APP_SECRET=
```