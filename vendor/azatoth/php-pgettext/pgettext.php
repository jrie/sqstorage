<?php

/* These functions are related to the normal gettext functions except that they are
 * taking an context variable as well */
function pgettext($msg_ctxt, $msgid) {
	return dpgettext(textdomain(null), $msg_ctxt, $msgid);
}

function dpgettext($domain, $msg_ctxt, $msgid) {
	return dcpgettext($domain, $msg_ctxt, $msgid, LC_MESSAGES);
}

function dcpgettext($domain, $msg_ctxt, $msgid, $category) {
	$msg_ctxt_id = "{$msg_ctxt}\004{$msgid}";
	$translation = dcgettext( $domain, $msg_ctxt_id, $category);
	if( $translation == $msg_ctxt_id ) {
		return $msgid;
	} else {
		return $translation;
	}
}

function npgettext($msg_ctxt, $msgid, $msgid_plural, $n) {
	return dnpgettext(textdomain(null), $msg_ctxt, $msgid, $msgid_plural, $n);
}

function dnpgettext($domain, $msg_ctxt, $msgid, $msgid_plural, $n) {
	return dcnpgettext($domain, $msg_ctxt, $msgid, $msgid_plural, $n,  LC_MESSAGES);
}

function dcnpgettext($domain, $msg_ctxt, $msgid, $msgid_plural, $n, $category) {
	$msg_ctxt_id = "{$msg_ctxt}\004{$msgid}";
	$translation = dcngettext( $domain, $msg_ctxt_id, $msgid_plural, $n, $category);
	if( $translation == $msg_ctxt_id || $translation == $msgid_plural ) {
		return $n == 1 ? $msgid : $msgid_plural;
	} else {
		return $translation;
	}
}

/* PHP also lacks gettext_noop; Even though this isn't an simple macro expansion as
 * it is in the c code, I assume it won't affect the performance much when used. */
function gettext_noop($msgid) {
	return $msgid;
}
