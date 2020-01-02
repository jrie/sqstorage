#!/bin/bash

MYIFS=$IFS
IFS="
"

TARGETTEMPLATE="templates/php_language_extract.tpl"


ALL=$(grep gettext *.php | sed -e "s/\n//g"  |  sed -e "s/gettext/\ngettext/g" | grep "gettext('" | cut -d "'" -f 2 | sort -u)

for PHRASE in $ALL
do

term='{t}'"$PHRASE"'{/t}'
if [ $(grep "$term" "$TARGETTEMPLATE" | wc -l) -eq 0 ]
then

echo "$term" 
echo "$term" >> "$TARGETTEMPLATE"

fi






done
IFS=$MYIFS
