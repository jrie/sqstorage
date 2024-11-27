# pgettext and buddies

Default installation of `gettext` in php lacks support for `pgettext` et al.
This php script adds support for those, as well as an `gettext_noop` wrapper.

For this to work when extracting strings using `xgettext`, many keyword parameters needs to be filed,
A wrapper script called `php-xgettext` might be used to simplify this.

Following functions are exported:

* `pgettext($msg_ctxt, $msgid)`
* `dpgettext($domain, $msg_ctxt, $msgid)`
* `dcpgettext($domain, $msg_ctxt, $msgid, $category)`
* `npgettext($msg_ctxt, $msgid, $msgid_plural, $n)`
* `dnpgettext($domain, $msg_ctxt, $msgid, $msgid_plural, $n)`
* `dcnpgettext($domain, $msg_ctxt, $msgid, $msgid_plural, $n, $category)`
* `gettext_noop($msgid)`
