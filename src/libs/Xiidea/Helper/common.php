<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

function __t($string, $domain = FALSE)
{
    echo _t($string, $domain);
}

function _t($string, $domain = FALSE)
{
    $CI = & get_instance();
    return $CI->lang->line($string, $domain);
}

function arr_copy_by_key($ref = array(), $key = array())
{
    return array_intersect_key($ref, array_fill_keys($key, ''));
}

function l_date($format, $date = FALSE)
{
    $timestamp = $date ? $date : time();
    return strftime(dateFormatToStrftime($format), $timestamp);
}

function dateFormatToStrftime($dateFormat)
{
    $caracs = array(
        // Day - no strf eq : S
        'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
        // Week - no date eq : %U, %W
        'W' => '%V',
        // Month - no strf eq : n, t
        'F' => '%B', 'm' => '%m', 'M' => '%b',
        // Year - no strf eq : L; no date eq : %C, %g
        'o' => '%G', 'Y' => '%Y', 'y' => '%y',
        // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
        'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
        // Timezone - no strf eq : e, I, P, Z
        'O' => '%z', 'T' => '%Z',
        // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
        'U' => '%s'
    );

    return strtr((string)$dateFormat, $caracs);
}

function nonce_tick()
{
    return ceil(time() / (12 * 60 * 60));
}

function nonce($str)
{
    $CI = & get_instance();
    $i  = nonce_tick();
    return substr(md5($i . $str . $CI->session->userdata('user_id') . $CI->config->item('encryption_key')), -12, 10);
}

function valid_nonce($str, $key)
{
    return ($key == nonce($str));
}

