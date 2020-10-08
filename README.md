![Logo sqStorage](https://www.picflash.org/img/2018/12/31/hwxkb96wq17sfvu.png "Logo sqStorage")

A easy to use and super quick way to organize your inventory, storage and storage areas.

### Note
Right now sqStorage is available in German and English. Feel free to add your own translation (see LANGUAGE.md for details on how to do so).

### Installation and usage

#### Requirements

* PHP version 7.0 and upwards
  * PHP extensions: `mysqli`, `gettext`, `intl`
* a MySQL-compatible database server (e.g. MariaDB)
* a web server, e.g. nginx or Apache.

#### Installation
To install sqStorage perform the following steps
* Download the files
* Place the files in the target directory (accessible for web-server)
* Set the required folder permission
	* The webserver required write permissions to the following directories  
	* `smartyfiles/` ,`languages/locale/` and `support/`
	* `chown -R www-data smartyfiles/` , `chown -R www-data languages/locale/` and `chown -R www-data support/` should work in most cases.
* Visit sqStorage with your browser and you will be redirected to the install page
	* Select whether you want to use pretty urls (rewrite module for the webserver is required to be activated)
	* Select whether you want to use the registration and login system
	*  Enter the database credentials 
	*  Save the credentials
	*  Click the install / update button

Your sgStorage installation is completeled

#### Update your installation
* Download the files and replace the existing  with the new ones
* Visit the install.php page of your installation with your browser
* Click the install / update button


#### Settings
All this settings can be configured in `support/dba.php`
##### Database

Default database: `tlv`
Default username: `tlvUser`
Default password: `tlvUser`
Default server: `localhost`
Default port: `3306`
Default useRegistration: `false`
Default usePrettyURLs: `true`

***Please note the user registration and login/logout*** can be enabled by setting the variable `$useRegistration` to `true`, otherwise the default disables this feature by setting this to `false`.

Also `usePrettyURLs` can be set to `false` in order to disable pretty urls. ***This might resolve some errors on Raspberry OS***.

##### Permissions

The directories `smartyfiles/` , `support/` and `languages/locale/` need to be **writeable** for the webserver.

`chown -R www-data smartyfiles/`, `chown -R www-data support/` and `chown -R www-data languages/locale/` should work in most cases.

#### German talking src ressource
The whole idea behind sqStorage or "Tom's Inventarverwaltung" can be found at the german bulletin board NGB.to over https://ngb.to/threads/39122-Webbasierte-Mini-Lagerverwaltung

#### Chatroom
There is a **Matrix chat room** at Tilde.fun where development and or general talk about features and such can be brought in:
https://chat.tilde.fun/#/room/#sqstorage:tilde.fun

#### Support the development
Finally there is a way, besides providing feedback, to feed back and support the development of sqStroage using PayPal.me at https://paypal.me/dwroxnet

#### Last but not least
Have fun using sqStorage and do not hesitate to write a email or issue, if you miss something!
