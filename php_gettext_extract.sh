#!/bin/bash

src="')"
rpl="\n"

grep gettext *.php | sed -e "s/\n//g"  |  sed -e "s/gettext/\ngettext/g" | grep gettext