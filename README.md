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

#### Database

Default database: `tlv`

Default username: `tlvUser`

Default password: `tlvUser`

Default server: `localhost`

Default port: `3306`

Default useRegistration: `false`

Default usePrettyURLs: `true`

All this settings can be configured in `support/dba-example.php` by changing the `DB::dbName`, `DB::$user` and `DB::$password` variables. 


***Please note the user registration and login/logout*** can be enabled by setting the variable `$useRegistration` to `true`, otherwise the default disables this feature by setting this to `false`.


Also `usePrettyURLs` can be set to `false` in order to disable pretty urls. ***This might resolve some errors on Raspberry OS***.


If your database is on a different server, you might want to use the IP or hostname instead of `localhost`. Afterwards make a copy of `dba-example.php` and rename it to `dba.php` in order for sqStorage to read out this configuration file.

#### Permissions and error 500

The directories `smartyfolders/` and `languages/locale/` need to be **writeable and readable** for the webserver. This also fixes error 500 in some circumstances - please see your webserver log access and error report in case of questions.

```
chown -R www-data smartyfolders/
chown -R www-data languages/locale/
```

should work in most cases.

#### First run

- Once user and database are created, open `bootDB.php`. This will create all DB tables necessary.
- Open sqstorage in your webbrowser and create a admin account.
  * this can be done only once after installation!
  * If you mess up, you will have to drop/truncate the `users` table in order to prompt for admin account registration again. You will have to open `bootDB.php` again to recreate the tables.
  
#### Custom fields

If you are upgrading of an earlier version of sqStorage, the custom fields code might have changed. This fields had been implemented earlier but where of no practical use. Still possible so, you might have to **update your database** in order to make use of the latest features.

##### Updating the database for usage of custom fields
In any case it is a good idea to open the database and **dropping** the `customFields` and `fieldData` tables. After dropping the tables, visit or execute `bootdb.php` to let the tables be created. After that, you can use custom fields.

##### Updating the database for upload of images for items
If not `images` are present in the database, simply open `bootDB.php`, afterwards image upload for items is available.


#### German talking src ressource
The whole idea behind sqStorage or "Tom's Inventarverwaltung" can be found at the german bulletin board NGB.to over https://ngb.to/threads/39122-Webbasierte-Mini-Lagerverwaltung

#### Chatroom
There is a **Matrix chat room** at Tilde.fun where development and or general talk about features and such can be brought in:
https://chat.tilde.fun/#/room/#sqstorage:tilde.fun

#### Support the development
Finally there is a way, besides providing feedback, to feed back and support the development of sqStroage using PayPal.me at https://paypal.me/dwroxnet

#### Last but not least
Have fun using sqStorage and do not hesitate to write a email or issue, if you miss something!
