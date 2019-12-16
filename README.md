![Logo sqStorage](https://www.picflash.org/img/2018/12/31/hwxkb96wq17sfvu.png "Logo sqStorage")

A easy to use and super quick way to organize your inventory, storage and storage areas.

### Note
At the moment sqStorage is only available in German, a translation to English is work in progress and to be come.

### Installation and usage
1) By default, the database name used is **tlv** and the main user **tlvUser** with the password **tlvUser** - this can be configured in **support/dba.php** changing the ***DB::dbName***,  ***DB::$user*** and ***DB::$password*** variables, if you use a server, you might want to use the SQL Server IP instead of *localhost*.

2) Once the user and database are created, open `bootDB.php` this will create all db tables ready for usage.

3) Open sqstorage and create a admin account - this can be done once after installation. If you mess up, youhave to drop/truncate the following tables.

The tables are:
* usergroups
* users

Then open `bootDB.php` again to recreate the tables.

4) Have fun!

