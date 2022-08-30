<?php


$statefiles=[
    'install' => "./support/installstate.txt",
    'other'   => "./support/nonexistingfile",

];
$statefile = $statefiles['other'];

if(isset($_GET['state'])){
      if(isset($statefiles[$_GET['state']])) $statefile = $statefiles[$_GET['state']];
}

if(!file_exists($statefile)) {
  echo "";
die;
}

$logoutput = file_get_contents($statefile);
echo $logoutput;


