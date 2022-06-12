<?php

//require_once("../../includer.php");

/**
 * DB Migration
 *
 * Current Revision saved in database_rev with id = 1
 *
 * Will iterate through all dbm_[REVISION].php script in support/database_migration folder
 * if REVISION greater than the current revision, the file will be included and database_rev updated
 *
 *
 */

DB::$usenull = false;





function DBMigration(){
  global $MigMessages;
  // versions control ini
  DB::query('CREATE TABLE IF NOT EXISTS `database_rev` ( `id` INT NOT NULL AUTO_INCREMENT , `dbrev` INT NOT NULL , PRIMARY KEY (`id`), UNIQUE `dbr` (`dbrev`)) ENGINE = InnoDB;');
  // check database revision
  $currentRev = DB::queryFirstField("Select dbrev from database_rev LIMIT 0,1");
  if($currentRev === null) {
    $currentRev = "0";
    UpdateRevEntry($currentRev);
  }
  MigrationWork( $currentRev);
};


/**
 *
 */
function MigrationWork($currentRevision){
  global $MigMessages, $basedir;
    if($currentRevision == 0){
          MigrationFromOldBase();
    }
    foreach (glob("$basedir"."/support/database_migration/dbm_*.php") as $filename) {
      $fn = basename($filename,".php");
      list($dump,$rev) = explode("_",$fn);
      $rev = $rev * 1;
      $Migrations[$rev] = $filename;
    }
    ksort($Migrations);
    $i=0;
    foreach($Migrations as $dbrev => $migrationfile){
        if($dbrev > $currentRevision){
          $i++;
          include_once($migrationfile);
          UpdateRevEntry($dbrev);
        }
    }










    $MigMessages[]= gettext("Datenbank ist auf dem aktuellen Stand");
    if($i == 0){
      $MigMessages[]= gettext("keine Änderungen durchgeführt");

    }else{
      $MigMessages[]= gettext("Alte Revision:") . $currentRevision ;
      $MigMessages[]= gettext("Neue Revision:") . $dbrev ;

    }

}
/**
 *
 */
function MigrationFromOldBase(){
   //run only if old base exists
  $tbls = DB::tableList();
  if (in_array("customfields",$tbls)){
    $cff = DB::columnList("customfields");
    // if field "defaultVisible" isn't available, it's the old version where customfields wasn't really used
    // so we can drop the tables customfields and fielddata before running the basic install procedure
    if(!in_array("defaultVisible",$cff)){
        DB::query("Drop table customfields");
        DB::query("Drop table fielddata");
    }
   }
}
/**
 *
 */
function UpdateRevEntry($rev){
  DB::insertUpdate("database_rev",array("id" => 1 , "dbrev" => $rev));
}
/**
 *
 */
