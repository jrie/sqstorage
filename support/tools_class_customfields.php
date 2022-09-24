<?php


class CF {
  /**
 *  CustomFields public static functions
 *  GetDataFields($tocheck,$cfconf,$cfdata,$itemid) Return all custom datafields for an item
 *  GetItemBasedCFD($customFieldsRaw)
 *     retvat[itemID][customFieldID]
 *  GetCustomFieldsConfiguration($cfs)
 *      workdb [ CategoryID oder All][customFieldsID]
 *
 */
  public static function GetDataFields($tocheck,$cfconf,$cfdata,$itemid){
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


  public static function GetItemBasedCFD($customFieldsRaw){
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


  public static function GetCustomFieldsConfiguration($cfs){
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
 *  End CustomFields public static functions
 */

}
