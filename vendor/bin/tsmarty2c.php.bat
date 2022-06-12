@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../smarty-gettext/smarty-gettext/tsmarty2c.php
php "%BIN_TARGET%" %*
