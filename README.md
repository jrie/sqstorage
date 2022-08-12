![Logo sqStorage](https://dwrox.net/sqstorage.png "Logo sqStorage")

A easy to use and super quick way to organize your inventory, storage and storage areas.

### Official website

There is a new website at https://sqstorage.net where you can actively support the development by renting paid hosting and support. This offer is for private and business users equally. Also, you can get a subdomain at `yourname`.sqstorage.net and password protected access in addition to the optional user system of sqStorage. If you do not feel like renting hosting and support, you always can make a donation using PayPal.me at https://paypal.me/dwroxnet .

### Note
Right now sqStorage is available in German and English. Feel free to add your own translation.
See [LANGUAGE.md](LANGUAGE.md) for details on how to do so.

### Installation and usage

#### Requirements

* PHP version 7.0 and upwards
  * PHP extensions: `mysqli`, `gettext`, `intl`
  * PHP extension `gd` on Raspberry OS
* a MySQL-compatible database server (e.g. MariaDB)
* a web server, e.g. nginx or Apache.

#### Installation
To install sqStorage perform the following steps:
* Download the files
* Place the files in the target directory (accessible for the web-server)
* Set the required folder permission
	* The webserver required write permissions to the following directories  
	* `smartyfiles/` ,`languages/locale/` and `support/`
	* On Linux `chown -R www-data smartyfiles/`, `chown -R www-data languages/locale/` and `chown -R www-data support/` should work in most cases. Alternatively you can `chmod +x setfolderpermssions.sh` and execute using `./setfolderpermssions.sh` if you are using bash.
  
* Visit sqStorage with your browser and you will be redirected to the install page
	* Select whether you want to use pretty urls (rewrite module for the webserver is required to be activated)
	* Select whether you want to use the registration and login system
	*  Enter the database credentials 
	*  Save the credentials
	*  Click the install / update button

Your sgStorage installation is completed.

#### Update your installation
* Download the files and replace the existing  with the new ones
* Visit the install.php page of your installation with your browser
* Click the install / update button

#### Enable / disable the installation/update
The ***Installer*** `install.php` is only enabled if a file named `allow_install` exists within the `support/` directory. 
This file is installed by default. 
If sqStorage is accessible from outside your home network, you should delete this file either
* manually after the installation is completed or
* by using `settings.php` (login feature enabled)
To later update your installation, simple create the file manually


### Manual configuration/settings
All this settings can be configured in `support/dba.php`

#### Database

Default database: `tlv`

Default username: `tlvUser`

Default password: `tlvUser`

Default server: `localhost`

Default port: `3306`

Default useRegistration: `false`

Default usePrettyURLs: `true`

***Please note the user registration and login/logout*** can be enabled by setting the variable `$useRegistration` to `true`, otherwise the default disables this feature by setting this to `false`.

Also `$usePrettyURLs` can be set to `false` in order to disable pretty urls. ***This might resolve some errors on Raspberry OS***.

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

#### First run

- Once you installation is completed, visit the main page `index.php` to open sqStorage
- If you decided to use the login feature, you will be asked to create an admin account.
  * this can be done only once after installation!
  * If you mess up, you will have to truncate the `users` table in order to prompt for admin account registration again.
  
#### Custom fields and image upload

If you are upgrading of an earlier version of sqStorage, 
* the custom fields code might have changed. This fields had been implemented earlier but where of no practical use. 

* the option to upload images was added

Still possible so, you might have to **update your database** by visiting the `install.php` script and updating your database in order to make use of the latest features.

### REST-API
sqStorage provides a REST-API for data access and manipuluation.
See [REST_API.md](REST_API.md) for details on how to use it.

### Troubleshooting
`Fatal error: Uncaught Error: Class 'Locale' not found` If this error message is shown, the php package intl is not activated. If you're using Windows and XAMPP to run this app, you can enable it by editing the php.ini file in your XAMPP-php directory (Standard-installation: `C:\xampp\php\php.ini`).
Remove the semicolon in front of 
`;extension=php_intl.dll`
and restart the Apache webserver.

##### Language selection in Windows XAMPP
If you're running a Windows XAMPP development system, you need to start xampp-control by command line. Start the command line [WIN+R -> cmd.exe] and enter the command `set LANG=en_GB` (or de_DE, or ... you know) and start xampp-control `c:\xampp\xampp-control.exe`

### German talking src ressource
The whole idea behind sqStorage or "Thom's Inventarverwaltung" can be found at the german bulletin board NGB.to over https://ngb.to/threads/39122-Webbasierte-Mini-Lagerverwaltung

#### Support the development
Finally there is a way, besides providing feedback, to feed back and support the development of sqStroage using PayPal.me at https://paypal.me/dwroxnet

### Last but not least
Have fun using sqStorage and do not hesitate to write a email or issue, if you miss something!
