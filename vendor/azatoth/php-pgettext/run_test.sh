#!/bin/bash

echo "before catalog generation"
php test.php
./php-xgettext --from-code="UTF-8" --package-name="test" --package-version="1.0" --output=messages.pot test.php

msgmerge sv.po messages.pot  -U
msgfmt sv.po
mkdir sv/LC_MESSAGES -p
mv messages.mo sv/LC_MESSAGES/
echo "after catalog generation"
php test.php

rm -rf sv messages.*
