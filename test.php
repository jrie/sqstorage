<?php





function fGetDBCreds($label){
  static $dba;
  if($dba == ""){
    $dba = file_get_contents("./support/dba.php");
  }
  $exp = explode("\n",$dba);
  for($x=1;$x < count($exp);$x++){
      $term = trim($exp[$x]);
      if( substr($term,0,strlen($label)) == $label){
              list($var,$val) = explode("=",$term);
              $val = trim($val);
              $val = substr($val,0,strlen($val)-1);
              $val = trim($val);
              $val =  substr($val, 1, strlen($val)-2 ) ;
                            return stripslashes($val);


      }
    }
}





$out[]= "--". fGetDBCreds('DB::$password') . "--";
$out[]= "--". fGetDBCreds('DB::$user') . "--";
$out[]= "--". fGetDBCreds('DB::$dbName') . "--";
$out[]= "--". fGetDBCreds('DB::$port') . "--";
$out[]= "--". fGetDBCreds('DB::$host') . "--";





echo "<pre>" . print_r([$out],true) . "</pre>";



