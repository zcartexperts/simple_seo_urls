INSTALLATION

Steps:

1. If you have already modified the file: includes/functions/html_output.php, please compare and merge it with the one that comes with this module.

2. Rename the folder "YOUR_ADMIN" to your admin folder name.

3. Upload all the files maintaining the folder structure.

4. Patch your database with the included "install.sql".

5. Go to Admin->Configuration->Simple SEO URL Configuration and turn on the module.

6. If you want to reset the url's, or clear the generated cache, go to Admin->Extras->Simple SEO URL Manager


WARNING:

A. If your path_to_store/.htaccess file is not blank, do NOT overwrite it. Open the .htaccess file and append the following lines:

Rename "RewriteBase /zencart/" to reflect your zen cart installation folder name. 


#### BOF SSU


Options +FollowSymLinks -MultiViews
RewriteEngine On
# Make sure to change "zencart" to the subfolder you install ZC. If you use root folder, change to: RewriteBase / 
RewriteBase /zencart/

# Deny access from .htaccess
RewriteRule ^\.htaccess$ - [F]

RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
#RewriteRule ^(.*) index.php?/$1 [E=VAR1:$1,QSA,L]
RewriteRule ^(.+) index.php/$1 [E=VAR1:$1,QSA,L,PT]


#### EOF SSU


This mod use the cache folder path defined in: DIR_FS_SQL_CACHE in file includes/configure.php

This folder is usually located at the root of your ZC store (http://site.com/path_to_store/cache)

    If it is NOT, you will find in the SSU package the folder cache/ssu, copy this ssu folder along with its sub folders into your cache folder.
    Go to path_to_cache_folder/ssu, set this “ssu” folder's and all its subfolders permission to 0777
    It is VERY important to check and make sure that the values of DIR_FS_SQL_CACHE defined at the bottom of includes/configure.php and admin/includes/configure.php are the SAME.

For most users, SSU can be run “as is”, but you may want to take a look at this configuration guide to see what SSU allows you to do. names as well)
