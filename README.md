# sqStorage
![Logo sqStorage](https://dwrox.net/imgs/sqstorage.webp "Logo sqStorage")

## A free and open source web based inventory and storage manager
*A easy to use and super quick way to organize your inventory, storage and storage areas or any other kind of collections, like music, movie or video games.*

## Official website

The official sqStorage website is located at [https://sqstorage.net](https://sqstorage.net)

### Online interactive live demo
A live demo is also available, including the current development state. The live demo can be found at [sqStorage live demo](https://sqstorage.net/#livedemo).

## Support development
You can actively support the development by renting paid hosting and support.
This offer is for private and business users equally. Also, you can get a subdomain at ***`yourname`.sqstorage.net***, and password protected access in addition to the optional user system of sqStorage.

If you do not feel like renting hosting and support, you always can make a donation using PayPal.me at [https://paypal.me/dwroxnet](https://paypal.me/dwroxnet).

### About available languages and language extensions
Right now sqStorage is available in *German*, *English* and *Polish*.
Feel free to add your own translation.

See [LANGUAGE.md](LANGUAGE.md) for details on how to do so.


## Installation and usage (manually, for docker follow [docker-installation.md](docker-installation.md))

### Requirements

* PHP version 8.2 and upwards
  * PHP extensions: `mysqli`, `gettext`, `intl`,
  * PHP extension `gd`
* a MySQL-compatible database server (e.g. MariaDB) or *SQLite3
* a web server, e.g. nginx, Apache or *lighttpd.

**Note for lighttpd**: In order to use `lighttpd` you currently have to disable pretty urls on installation, or in case of a exsiting installation, change the option is in your `dba.php`. Set `$prettyURLs = false;`

**Note for SQLite**: SQLite is currently useable by a predefined, empty, database file located in `support/sqlite_db.sq3` - there is no setup script, the database is prepared and basically ready to use. You just have to adapt the `dba-example.php` and name it `dba.php` to target the `sqlite:/path/to/database/support/sqlite_db.sq3`. Also, the PHP SQLite and PDO-SQLite driver have to be installed in order for `meekrodb` to do its magic.

### Installation
To install sqStorage perform the following steps:
* Download the files using git: `git clone --recurse-submodules https://github.com/jrie/sqstorage`
* If not done before, update the `meekrodb` git submodule: `git submodule init` and `git submodule update`
* Place the files in the target directory (accessible for the web-server)
* Set the required folder permission
	* The webserver required write permissions to the following directories
	* `smartyfiles/` ,`languages/locale/` and `support/`
	* On Linux `chown -R www-data smartyfiles/`, `chown -R www-data languages/locale/` and `chown -R www-data support/` should work in most cases. Alternatively you can `chmod +x setfolderpermssions.sh` and execute using `./setfolderpermssions.sh` if you are using bash.

* Visit sqStorage with your browser and you will be redirected to the install page
	* Select whether you want to use pretty urls (rewrite module for the webserver is required to be activated, does currently **not** work for lighttpd)
	* Select whether you want to use the registration and login system
	* Enter the database credentials
	* Enter the database administrator username and password
	* Save the credentials
	* Click install and the sqStorage database and user are created
	* In the last step, click to "update the database" for the current revision.

Your sgStorage installation is completed.

### Update your installation
* Download the files and replace the existing  with the new ones
* If not done, update the meekrodb git submodule: `git submodule init` and `git submodule update`
* Visit the install.php page of your installation with your browser
* Click the install / update button

### Enable / disable the installation/update
The ***Installer*** `install.php` is only enabled if a file named `allow_install` exists within the `support/` directory.
This file is installed by default.
If sqStorage is accessible from outside your home network, you should delete this file either
* manually after the installation is completed or
* by using `settings.php` (login feature enabled)
To later update your installation, simple create the file manually


## Manual configuration and other settings
All this settings can be configured in `support/dba.php`

### Database

Default database: `tlv`

Default username: `tlvUser`

Default password: `tlvUser`

Default server: `localhost`

Default port: `3306`

Default useRegistration: `false`

Default usePrettyURLs: `true`

***Please note the user registration and login/logout*** can be enabled by setting the variable `$useRegistration` to `true`, otherwise the default disables this feature by setting this to `false`.

If you are planning to use `$usePrettyURLs` on **Raspberry OS** please ensure that the apache2 site configuration allows the usage of `.htaccess`.
This can be achieved by adding
```
<Directory /var/www/html>
	Options Indexes FollowSymLinks
	AllowOverride All
	Require all granted
</Directory>
```
to the `/etc/apache2/sites-enabled/000-default.conf` site configuration (don't forget to restart apache afterwards `systemctl restart apache2`)

Alternatively `$usePrettyURLs` can be set to `false` in order to disable pretty urls. ***This might resolve some errors on Raspberry OS***.

### PHP Error: Class "DB" not found on first installation / download
See this issue ["Class "DB" not found"](https://github.com/jrie/sqstorage/issues/106).
This is due to the fact that "meekrodb" is referenced as a submodule and not added as a fixed part in the downloaded package *or* on default `git clone` command. See also this [topic on Stackoverflow](https://stackoverflow.com/questions/34719785/how-to-add-submodule-files-to-a-github-release).

#### Permissions and error 500

The directories `smartyfiles/` , `support/` and `languages/locale/` need to be **writeable** for the webserver.

```
chown -R www-data smartyfolders/
chgrp -R www-data smartyfolders/

chown -R www-data support/
chgrp -R www-data support/

chown -R www-data languages/locale/
chgrp -R www-data languages/locale/
```

should work in most cases.

### First run

- Once you installation is completed, visit the main page `index.php` to open sqStorage
- If you decided to use the login feature, you will be asked to create an admin account.
  * this can be done only once after installation!
  * If you mess up, you will have to truncate the `users` table in order to prompt for admin account registration again.

### Custom fields and image upload

If you are upgrading of an earlier version of sqStorage,
* the custom fields code might have changed. This fields had been implemented earlier but where of no practical use.

* the option to upload images was added

Still possible so, you might have to **update your database** by visiting the `install.php` script and updating your database in order to make use of the latest features.

## REST-API
sqStorage provides a REST-API for data access and manipuluation.
See [REST_API.md](REST_API.md) for details on how to use it.

## Troubleshooting
`Fatal error: Uncaught Error: Class 'Locale' not found` If this error message is shown, the php package intl is not activated. If you're using Windows and XAMPP to run this app, you can enable it by editing the php.ini file in your XAMPP-php directory (Standard-installation: `C:\xampp\php\php.ini`).
Remove the semicolon in front of
`;extension=php_intl.dll`
and restart the Apache webserver.

## Language selection in Windows XAMPP
If you're running a Windows XAMPP development system, you need to start xampp-control by command line. Start the command line [WIN+R -> cmd.exe] and enter the command `set LANG=en_GB` (or de_DE, or ... you know) and start xampp-control `c:\xampp\xampp-control.exe`

## German talking src ressource
The whole idea behind sqStorage or "Thom's Inventarverwaltung" can be found at the german bulletin board NGB.to over https://ngb.to/threads/39122-Webbasierte-Mini-Lagerverwaltung

## Last but not least
Have fun using sqStorage.
