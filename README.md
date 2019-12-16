![Logo sqStorage](https://www.picflash.org/img/2018/12/31/hwxkb96wq17sfvu.png "Logo sqStorage")

A easy to use and super quick way to organize your inventory, storage and storage areas.

### Note
Right now sqStorage is only available in German, a English translation is due to happen at some point in near future!

### Installation and usage
1) By default, the database name used is **tlv** and the main user **tlvUser** with the password **tlvUser** - this can be configured in **support/dba.php** changing the ***DB::dbName***,  ***DB::$user*** and ***DB::$password*** variables, if you use a server, you might want to use the SQL Server IP instead of *localhost*.

2) Once the user and database are created, open `bootDB.php` this will create all db tables ready for usage.

3) Open sqstorage and create a admin account - this can be done once after installation. If you mess up, you will have to drop/truncate the following tables in order to prompt for the admin account registration again.

The tables are:
* users

Then open `bootDB.php` again to recreate the tables.

Last but not least:

4) Have fun using sqStorage and do not hesitate to write a email or issue, if you miss something!

