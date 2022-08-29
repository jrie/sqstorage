<?php

/**
 *
 * Github updater and how it works:
 *
 * Check if an update is available
 * -AreFileUpToDate($ghuser,$ghrepo,$ghbranch,&$debugData = "")
 * --Step 1: Get the information about the most recent commit to a branch
 * --Step 2: Get the list of modified files - filename - SHA Checksum - modification time
 * --Step 3: Check the modified files against those available on the local installation
 * -------3a: Checksum comparison , if Checksum differes an update is available
 * -------3b: If all local files are older than those in the commit an update is available
 *            (required since the file could be changed back to an old state with a matching checksum )
 * --Step 4: If any checksum discrepancy occurs or all local files are older than those hosted on GitHub,
 *           the function will return FALSE
 *
 *
 * Update Files from Github
 * -DownloadMasterZipAndUnpack($user,$repo,$branch,$dir=__DIR__)
 * --Step 1: Downloads the most recent zip from the defined branch
 * --Step 2: Unpacks the zip file into the newly created temp folder ./unpack_temp_dir
 * --Step 3: Checks if all files contained in the zip can be written / created on the local file system
 * --Step 3a: If all files can be written, the extracted files will be copied into the target folders
 * --Step 3b: If at least one file can't be written, no file will be copied
 *
 *
 */



function DownloadMasterZipAndUnpack($user,$repo,$branch,$dir=__DIR__){
  $docopy = true;
  if(!file_exists($dir . "/support/" . $branch . ".zip")){
    $remoteurl = "https://github.com/$user/$repo/archive/".$branch.".zip";
    $filetmp = file_get_contents($remoteurl);
    file_put_contents($dir . "/support/" . $branch .".zip",$filetmp);
  }
  $len=strlen($repo . "-" .$branch . "/");
  $allwriteable = true;
  if(is_dir($dir . '/support/unpack_temp_dir')) rrmdir($dir . '/support/unpack_temp_dir');
  mkdir($dir . '/support/unpack_temp_dir');
  $strFT = "";
  $zip = new ZipArchive;
  if ($zip->open($dir . "/support/" . $branch .'.zip') === TRUE) {
      $zip->extractTo($dir . '/support/unpack_temp_dir/');
      $strFT .= "<table border='1'>";
      for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        $currfile = $dir . "/support/unpack_temp_dir/" . $filename;
        $newfile = $dir . DIRECTORY_SEPARATOR . substr($filename,$len);

        if(!file_exists($newfile)) {
          @touch($newfile);
          $unlink_afterwards = true;
        }else{
          $unlink_afterwards = false;
        }
        if(is_writable($newfile)){
          $write = "writeable";
          $pre = "";
          $aft = "";
          if($unlink_afterwards) unlink($newfile);
        }else{
          $pre = "<h3>";
          $aft = "</h3>";
          $write = "blocked";
          $allwriteable = false;
        }
        $strFT .= "<tr><td>$currfile</td><td>".$pre."$newfile". $aft ."</td><td>$write</td></tr>";
      }
      $strFT .= "</table>";
      if($allwriteable){
        $out = "<h2>All files can be written, permissions granted</h2>";
        for ($i = 0; $i < $zip->numFiles; $i++) {
          $filename = $zip->getNameIndex($i);
          $currfile = $dir . "/support/unpack_temp_dir/" . $filename;
          $newfile = $dir . DIRECTORY_SEPARATOR . substr($filename,$len);
          if($docopy){
            $lastchar = substr($currfile,-1);
            if($lastchar != "/" && $lastchar != "\\") {
              copy($currfile,$newfile);
            }else{
              if(strlen($newfile) > 0)@mkdir($newfile);
            }
          }
        }
      }else{
          $out = "<h2>Not all required files can be written, action aborted</h2>";
      }
      $zip->close();
      $out .=  $strFT;
      unlink($dir . "/support/" . $branch . '.zip');
      rrmdir($dir . '/support/unpack_temp_dir');
  } else {
      $out = "</h2>Error - Zip file couldn't be opened</2>";
  }
  return $out;
}

/**
*
*/
function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
          rrmdir($dir. DIRECTORY_SEPARATOR .$object);
        else
          unlink($dir. DIRECTORY_SEPARATOR .$object);
      }
    }
    rmdir($dir);
  }
  }
/**
 ****************************************************************************************************************************************
 ****************************************************************************************************************************************
 ****************************************************************************************************************************************
 */
function AreFileUpToDate($ghuser,$ghrepo,$ghbranch,&$debugData = ""){
  $out = true;
  $newercount = 0;
  $debugData .= "<table border='1'>";
  $debugData .= "<tr><th>File</th><th>Checksum</th><th>Filedate</th><th>CheckSumCheck</th><th>Filetimecheck</th></tr>";
  $commitdetails = GHGetLastCommitDetails($ghuser,$ghrepo,$ghbranch);
  $sha = $commitdetails['sha'];
  $lastcommitfiles = GHGetCommitFiles($ghuser,$ghrepo,$sha);
    foreach($lastcommitfiles as $filename => $filedetails){
      $debugData .= "<tr>";
      $checksum = $filedetails['checksum'];
      $editdate = $filedetails['date'];
      $editts = strtotime($editdate);
      $debugData .= "<td>$filename</td><td>$checksum</td><td>$editdate - $editts</td>";
      $localchecksum = GitFileHash($filename);
      $localtime = 0;
      if(file_exists($filename)) $localtime = filemtime($filename);
      if($localchecksum !== $checksum){
        $debugData .= "<td>MisMatch</td>";
        $out = false;
        $debugData .= "<td>Skipped, Checksum mismatch</td>";
      }else{
        $debugData .= "<td>Match</td>";
      }

      if($localtime > $editts){
        $lta = "Local file is newer";
        $newercount++;
      }else{
        $lta = "Local file is older";
      }
      $debugData .= "<td> $lta - $localtime</td>";
      $debugData .= "</tr>";
    }
    $debugData .= "</table>";

    if($newercount == 0){
        $debugData .= "<h3>All checked file older than in github - update should be performed</h2>";
        $out = false;
    }
  return $out;
}



function GHGetCommitFiles($ghuser,$ghrepo,$sha){
    $commits = array();
    $tmp = GHCUrl("https://api.github.com/repos/$ghuser/$ghrepo/commits/" . $sha);
    $tmp = json_decode($tmp,true);
    for($x=0;$x < count($tmp['files']); $x++){
        $comm = $tmp['files'][$x];
        $commits[$comm['filename']]['checksum'] = $comm['sha'];
        $commits[$comm['filename']]['date'] = $tmp['commit']['committer']['date'];
    }
    return $commits;
}


function GHGetLastCommitDetails($ghuser,$ghrepo,$ghbranch)
{
  $GHUrl = "https://api.github.com/repos/$ghuser/$ghrepo/commits/$ghbranch";
  $tmp =json_decode(GHCUrl($GHUrl),true);
  $retData = array();
  $retData['sha'] = $tmp['sha'];
  $retData['date'] = $tmp['commit']['committer']['date'];
  return $retData;
}



/**
*
*/
function GHCUrl($url) {
  global $github_account;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_REFERER, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
/**
 * Helper
 */
function GitFileHash($file2check){
  if(!file_exists($file2check)) return false;
  global $lastmime;
    $cont=file_get_contents($file2check);
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $lastmime = $file_info->buffer($cont);
    if(strpos(".". $lastmime,'text/'))  $cont = str_replace("\r","" ,$cont);
    if($lastmime == "application/x-wine-extension-ini") $cont = str_replace("\r","" ,$cont);
    $len = mb_strlen($cont,'8bit');
    $toc ="blob " . $len . chr(0) .  $cont ;
    $tmp = sha1($toc);
    return $tmp;
  }


