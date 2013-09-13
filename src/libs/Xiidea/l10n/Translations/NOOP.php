<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/16/13
 * Time: 11:51 AM
 */

namespace Xiidea\l10n\Translations;

class NOOP {
    var $entries = array();
    var $headers = array();

    function add_entry($entry) {
        return true;
    }

    function set_header($header, $value) {
    }

    function set_headers(&$headers) {
    }

    function get_header($header) {
        return false;
    }

    function translate_entry(&$entry) {
        return false;
    }

    function translate($singular, $context=null) {
        return $singular;
    }

    function select_plural_form($count) {
        return 1 == $count? 0 : 1;
    }

    function get_plural_forms_count() {
        return 2;
    }

    function translate_plural($singular, $plural, $count, $context = null) {
        return 1 == $count? $singular : $plural;
    }

    function merge_with(&$other) {
    }
}