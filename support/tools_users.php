<?php


function AllowLogin($username,&$remaining_seconds){
  //auto update user table structure of failcount column is missing
  $remaining_seconds = 0;
  if(!in_array('failcount',DB::columnList('users'))) return true;


  $fc = DB::queryFirstRow('Select failcount, lastfail from users WHERE username = %s',$username);



  if (count($fc) == 0) return true;  //no user-record, no problem. Login will assign the error message
  $sinceLastFail = abs( time()-$fc['lastfail'] );
  if ( $sinceLastFail > 900 ) return true; // last failed login try happened more than 15 minutes ago, let's have another try
  if ($fc['failcount'] < 3) return true;  // 3 login tries without delay
  $waittime = 0;
  if ($fc['failcount'] < 9){
      $multi = 10;


      $waittime = $fc['failcount'] * $multi;
      if($sinceLastFail > $waittime) return true;

      $remaining_seconds = $waittime - $sinceLastFail;
      return false;

  }


  if ($fc['failcount'] < 9){
    sleep($fc['failcount']);
    return true;
  } // less than 10 tries? Why not slow down it slightly, by 1 second per fail



  $remaining_seconds = 900 - $sinceLastFail;
  return false; // still not allowd? So better say 'computer says no'
}

function RegisterFail($userid){
  if(!in_array('failcount',DB::columnList('users'))) return true;
  $ofc = DB::queryFirstField('Select failcount from users WHERE id=%i',$userid);

  $ofc = $ofc + 1;
  DB::update('users',[ 'failcount' => $ofc, 'lastfail' => time() ], 'id=%i',$userid);
  return true;
}

function ResetFail($userid){
  if(!in_array('failcount',DB::columnList('users'))) return true;
  DB::update('users',[ 'failcount' => 0, 'lastfail' => 0 ], 'id=%i',$userid);
  return true;
}

function Login($username,$password,&$error){
  $remaining = 0;
  if(!AllowLogin($username,$remaining)){
    $error = gettext('Zu viele Anmeldeversuche. Bitte später nochmal versuchen');
    $error .= "<br />" . str_replace('XXX',$remaining, gettext('Anmeldung für weitere XXX Sekunden gesperrt'));

    return false;
  }
  $user = DB::queryFirstRow('SELECT u.id, u.username, u.password, g.usergroupid FROM users u LEFT JOIN users_groups g ON(g.userid=u.id) WHERE u.username=%s LIMIT 1', $username);
  if ($user && password_verify($password, $user['password'])) {
    ResetFail($user['id']);
    $_SESSION['authenticated'] = true;
    $_SESSION['user'] = ['id' => $user['id']];
    return true;
  } else {
    $error = gettext('Zugangsdaten ungültig');
    RegisterFail($user['id']);
    return false;

  }


}
