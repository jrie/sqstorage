<?php

$wsun="www-data";
if(function_exists("posix_getpwuid")){
$poss =  posix_getpwuid(posix_geteuid());
$wsun=$poss['name'];
}else{
  $wsun = get_current_user();
}

$writeable_folder = array("./smartyfolders/","./languages/locale/");

$unwriteable = array();
foreach($writeable_folder as $wf){
  if (!IsDirWriteable($wf)) $unwriteable[] = $wf;
}
if(count($unwriteable)>0){

  include_once("./support/language_tools.php");

  echo '<html><head><title>sqStorage - Installation</title><link rel="stylesheet" href="./css/bootstrap/bootstrap.css"><link rel="stylesheet" href="./css/base.css"><link rel="stylesheet" href="./fonts/fontawesome/css/solid.css"><link rel="stylesheet" href="./fonts/fontawesome/css/regular.css"><link rel="stylesheet" href="./fonts/fontawesome/css/fontawesome.css"><meta charset="utf-8"></head><body>';
  $desel = '';
  $ensel = '';
  if(isset($_SESSION['lang'])){
        if($_SESSION['lang'] == 'en_GB') $ensel = 'selected="selected"';
        if($_SESSION['lang'] == 'de_DE') $desel = 'selected="selected"';
  }


  ?>
<nav class="navbar navbar-light bg-light">
    <a href="index.php"><img class="logo" src="./img/sqstorage.png" alt="sqStorage logo" /></a>

    <div class="dropdown">
         <select class="form-control mr-sm-2" name="lang">
                        <option value="en_GB" <?php echo $ensel; ?>>English</option>
                        <option value="de_DE" <?php echo $desel; ?>>Deutsch</option>
                    </select>
        <script type ="text/javascript">
            let langSelection = document.querySelector('select[name="lang"').addEventListener('change', function (evt) {
                let langValue = evt.target.options[evt.target.selectedIndex].value
                let srcUri = window.location.href.toString().replace(/.lang=.[^\&\/]*/, '')
                if (srcUri.indexOf('?') === -1) window.location.href = srcUri + '?lang=' + langValue
                else window.location.href = srcUri + '&lang=' + langValue
            })
        </script>
    </div>


</nav>
  <?php
  echo '</nav><center>';
  echo '<div class="alert alert-danger" role="alert">' . "<h4>". gettext("Zugriff auf folgende Verzeichnisse fehlgeschlagen:") . "</h4>";
  foreach($unwriteable as $unw){
    echo "<h4>" . $unw . "</h4>";
  }
  echo "</div>";
  echo '<div class="alert alert-info" role="alert">' . "<h4>". gettext("Unter Linux könnten folgende Befehle weiterhelfen") . "</h4>" ."</div>";
  foreach($unwriteable as $unw){
  echo '<div class="alert alert-light" role="alert">' ."<h4>sudo chown -R $wsun $unw</h4><h4>sudo chgrp -R $wsun $unw</h4></div>";
  }
  ?>
                    <form method="post">
                    <input type="submit" value="<?php echo gettext("Erneut prüfen"); ?>" class="btn form-control btn-info">
                    </form>
  <?php
  echo "</center>";
  echo '<footer class="footer"><script type="text/javascript">eval(unescape("%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%6a%61%6e%40%64%77%72%6f%78%2e%6e%65%74%3f%73%75%62%6a%65%63%74%3d%73%71%73%74%6f%72%61%67%65%22%20%63%6c%61%73%73%3d%22%62%74%6e%20%62%74%6e%2d%69%6e%66%6f%22%20%74%61%62%69%6e%64%65%78%3d%22%2d%31%22%3e%4b%6f%6e%74%61%6b%74%3c%2f%61%3e%27%29%3b"))</script><a class="btn btn-info" tabIndex="-1" target="_blank" href="https://github.com/jrie/sqstorage">Github</a></footer></script>';
  echo '</body></html>';
  exit();
}
