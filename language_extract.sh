#!/usr/bin/bash

MYIFS=$IFS
IFS="
"

STEP=0

if [ "$1" != "" ]
then
  STEP=$1
fi



if [ $STEP == "0" ]
then
  rm tmpout.txt 2>/dev/null
  ALLPHP=$(find . -name '*.php' | grep -v vendor | grep -v smartyfolders)
  for PHPFILE in $ALLPHP
  do
  echo $PHPFILE
  xgettext --from-code=UTF-8 "$PHPFILE" -L PHP -o tmpX.txt --no-wrap
  cat tmpX.txt >> tmpout.txt
done
  rm tmpX.txt
STEP=1
fi

if [ $STEP == "1" ]
then
    START=7
    LINES=$(grep "msgid" tmpout.txt  | sort -u)
    for LINE in $LINES
    do
#      echo "$LINE"
      LINELENGTH=${#LINE}
      let LENGTH=$LINELENGTH-$START-1
      EXTLINE=${LINE:$START:$LENGTH}
      if [ "$EXTLINE" != "" ]
      then
        OUTLINE='{t}'"$EXTLINE"'{/t}'
          ISBUGGY=$(echo "$OUTLINE" | grep -F '\"' | wc -l)
          if [ "$ISBUGGY" == "0" ]
          then
            ISIN=$(grep -F "$OUTLINE" templates/php_language_extract.tpl | wc -l)
            if [ "$ISIN" == "0" ]
            then
              echo "NEW: $OUTLINE"
              echo "$OUTLINE" >> templates/php_language_extract.tpl

            fi
          fi
      fi
    done
    STEP=2
fi

if [ "$STEP" == "2" ]
then
  vendor/smarty-gettext/smarty-gettext/tsmarty2c.php -o languages/sqstorage.pot templates/
  STEP=3
fi

if [ "$STEP" == "3" ]
then
  rm tmpout.txt 2>/dev/null
  STEP=4
fi

IFS=$MYIFS
