#!/bin/bash


echo "Commit Kommentar"
if [ "$1" != "" ]
then
TestEingabe="$*"
else
read TestEingabe
fi
if [ "$TestEingabe" == "" ]
then
echo "Kein Kommentar,da geht nichts"
else
git add .
git commit -m "$TestEingabe"
git push
echo "Add,Commit,Push done"
fi
