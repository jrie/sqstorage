<?php
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


function GetDataFields($tocheck,$cfconf,$cfdata,$itemid){

  //tocheck z.b. ['all',1,2]
  //cfconfig[ CategoryID oder All][customFieldsID][id/label/dataType/defau...]
  //cfdata
  $retval = array();
  foreach($tocheck as $catmark){
      if(isset( $cfconf[$catmark] )){
          foreach( $cfconf[$catmark] as $cfid  => $cfsdata  ){
              $dfval = $cfsdata['default'];

              //$dfval .= " (default)";

              if( isset($cfdata[$itemid][$cfid])  ){
                $dfval = $cfdata[$itemid][$cfid];
              }
              $retval[$cfsdata['label']] = $dfval;


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
