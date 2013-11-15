<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\l10n\Translations;

class NOOP
{
    var $entries = array();
    var $headers = array();

    function add_entry($entry)
    {
        return TRUE;
    }

    function set_header($header, $value)
    {
    }

    function set_headers(&$headers)
    {
    }

    function get_header($header)
    {
        return FALSE;
    }

    function translate_entry(&$entry)
    {
        return FALSE;
    }

    function translate($singular, $context = NULL)
    {
        return $singular;
    }

    function select_plural_form($count)
    {
        return 1 == $count ? 0 : 1;
    }

    function get_plural_forms_count()
    {
        return 2;
    }

    function translate_plural($singular, $plural, $count, $context = NULL)
    {
        return 1 == $count ? $singular : $plural;
    }

    function merge_with(&$other)
    {
    }
}