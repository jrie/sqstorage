<head>
  <title>sqStorage</title>
  <link rel="stylesheet" href="./css/bootstrap/bootstrap.css">
  <link rel="stylesheet" href="./css/base.css">
  <link rel="stylesheet" href="./fonts/fontawesome/css/solid.css">
  <link rel="stylesheet" href="./fonts/fontawesome/css/regular.css">
  <link rel="stylesheet" href="./fonts/fontawesome/css/fontawesome.css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  require_once('./includer.php');

  //    require_once('./support/dba.php');
  if ($usePrettyURLs) $smarty->assign('urlPostFix', '');
  else $smarty->assign('urlPostFix', '.php');
  ?>
</head>