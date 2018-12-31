# sqstorage
A easy to use and quick way to organize your inventory, storage and storage areas.

## Installation

In order for sqStorage to work, you have to import the database structure **tlv_empty.sql** using for example phpmyadmin into MariaDB.

## Configuration
By default, the database name used is *tlv* and the main user *tlvUser* with the password *tlvUser* - this can be configured in **support/dba.php** changing the ***DB::dbName***,  ***DB::$user*** and ***DB::$password*** variables.

