<?php

/**
 *  Miscellaneous functions
 *  CheckDBCredentials($host,$user,$password,$name,$port,$silent=false) -> checks database credentials for validity
 *  GetNonEmptyArrayValues($ArrayGet) -> cleans empty elements from array  ['',12,'','ab',''] -> [12,'ab']
 *  IsDBUpdateAvailable() -> Checks if a db-migration is available which isn't installed yet. Returns true/false
 */

function CheckDBCredentials($host,$user,$password,$name,$port,$silent=false){
  global $error;

  $tmp = error_reporting();
  error_reporting(0);
  $mysqli_connection = new MySQLi($host,$user,$password,"",$port);
  error_reporting($tmp);
  if ($mysqli_connection->connect_error) {
      if(!$silent)$error[] = gettext("Zugang wurde verweigert. Bitte überprüfe die Zugangsdaten");
      return false;
  }
  else {
    $query = "CREATE DATABASE IF NOT EXISTS " . $name ;
    if(mysqli_query($mysqli_connection, $query)){
      return true;
    } else {
      $error[] = gettext("Die gewählte Datenbank existiert nicht und konnte nicht erstellt werden");
      return false;
    }
  }
}


function GetNonEmptyArrayValues($ArrayGet){
  $out = array();
  for($x = 0; $x < count($ArrayGet);$x++){
      if($ArrayGet[$x] != "") $out[] = $ArrayGet[$x];
  }
  return $out;
}


function IsDBUpdateAvailable(){
  global $basedir;
    foreach (glob("$basedir"."/support/database_migration/dbm_*.php") as $filename) {
      $fn = basename($filename,".php");
      list($dump,$rev) = explode("_",$fn);
      $availablerev = $rev * 1;
    }
    $dbvers = DB::queryFirstField('SELECT MAX(dbrev) FROM database_rev');
    if($availablerev > $dbvers){
      return true;
    }
    return false;
}

/**
 * End Miscellaneous functions
 */


/**
 *  CustomFields Functions
 *  GetDataFields($tocheck,$cfconf,$cfdata,$itemid) Return all custom datafields for an item
 *  GetItemBasedCFD($customFieldsRaw)
 *     retvat[itemID][customFieldID]
 *  GetCustomFieldsConfiguration($cfs)
 *      workdb [ CategoryID oder All][customFieldsID]
 *
 */
function GetDataFields($tocheck,$cfconf,$cfdata,$itemid){
  //tocheck z.b. ['all',1,2]
  //cfconfig[ CategoryID oder All][customFieldsID][id/label/dataType/defau...]
  //cfdata
  $retval = array();
  foreach($tocheck as $catmark){
      if(isset( $cfconf[$catmark] )){
          foreach( $cfconf[$catmark] as $cfid  => $cfsdata  ){
              if( isset($cfdata[$itemid][$cfid])  ){
                $retval[$cfsdata['label']] = $cfdata[$itemid][$cfid];
              }else{
                $retval[$cfsdata['label']] = $cfsdata['default'];
              }
          }
      }
  }
  return $retval;
}


function GetItemBasedCFD($customFieldsRaw){
  // retvat[itemID][customFieldID]
    $fnames = array('intNeg','intPos','intNegPos','floatNeg','floatPos','string','selection','mselection');
    $cflookupfield = array();
    for($x = 0; $x < count($customFieldsRaw);$x++){
      $cflookupfield[ $customFieldsRaw[$x]['id'] ] = $customFieldsRaw[$x]['dataType'];
    }
    $out = array();
    $cfb = DB::query('SELECT * FROM fieldData');
    for($x=0;$x < count($cfb);$x++){
        $cf = $cfb[$x];
        $lookupfield = $fnames[ $cflookupfield[ $cf['fieldId'] ]  ];
        $out[$cf['itemId']][$cf['fieldId']] = $cf[$lookupfield];
    }

return $out;
}


function GetCustomFieldsConfiguration($cfs){
  // workdb [ CategoryID oder All][customFieldsID]
  $workcfsw = array();
  $cfsw = array();
  for ($x=0;$x < count($cfs); $x++){
      $cfsw[$cfs[$x]['id']] = $cfs[$x];
  }
  foreach($cfsw as $CustFldID => $CustFData){
    $assignedto = GetNonEmptyArrayValues( explode(";",$CustFData['visibleIn'])   );
    $cfsw[$CustFldID]['assi'] = $assignedto;
    for($x =0; $x < count($assignedto);$x++){
        if($assignedto[$x] == "-1"){
          $catkey = 'all';
        }else{
          $catkey = $assignedto[$x];
        }
      $workcfsw[$catkey][$CustFldID] = $CustFData;
    }
  }
  return $workcfsw;
}
/**
 *  End CustomFields Functions
 */

/**
 *   Settings Tools
 *   SettingsGet($namespace) -> get all namespace - settings in ass. arry
 *   SettingsGetSingle($namespace,$setting,$default="")  -> get a specific setting from namespace, if missing return default
 *   SettingsSet($namespace,$setting,$value) -> Saves a setting and value into the namespace, is namespace or setting doesn't exist it will be created
 *
 */
function SettingsGet($namespace){
  $res = DB::queryFirstRow('SELECT * FROM settings WHERE `namespace` LIKE %s ',$namespace);
  if($res === null) return $res;
  return json_decode($res['jsondoc'],true);
}

function SettingsGetSingle($namespace,$setting,$default=""){
$res = DB::queryFirstRow('SELECT * FROM settings WHERE `namespace` LIKE %s ',$namespace);
if($res === null) return $default;
$tmp = json_decode($res['jsondoc'],true);
if(!isset($tmp[$setting]))return $default;
return $tmp[$setting];
}

function SettingsSet($namespace,$setting,$value){
   $oldSettings = SettingsGet($namespace);
   if($oldSettings === null){
      $oldSettings = array();
      $update = false;
   }else{
      $update = true;
   }
   $oldSettings[$setting] = $value;
   $json = json_encode($oldSettings);
   if($update){
        DB::query('UPDATE settings SET jsondoc = %s WHERE `namespace`LIKE %s',$json,$namespace);
   }else{
        DB::query('INSERT INTO settings (`namespace`,`jsondoc`) VALUES (%s,%s)', $namespace,$json);
   }
}
/**
 * End Settings Tools
 */


 /**
  * User-Group and User Tools
  *
  * AssignUserToGroup($userid,$groupid) -> Assignes a group to a user, if user already in a group the record will be updated
  *
  */
function AssignUserToGroup($userid,$groupid){
      $isRegisteredUser = DB::queryFirstRow('SELECT * FROM users_groups WHere userid = %i LIMIT 1',$userid);
      if($isRegisteredUser === null){
          DB::insert('users_groups', ['userid' => $userid, 'usergroupid' => $groupid ]);
      }else{
          DB::query('UPDATE users_groups SET usergroupid = %i WHERE userid = %i',$groupid,$userid);
      }
}

function DeleteUser($userid){
      DB::query('DELETE FROM users_groups WHERE userid = %i',$userid);
      DB::query('DELETE FROM users WHERE id=%i', $userid);
}


  /**
   * End User-Group Tools
   */


