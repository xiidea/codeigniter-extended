<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\l10n\POMO;

use Xiidea\l10n\Translations\Entry;
use Xiidea\l10n\Translations\Gettext;

/**
 * Class for working with MO files
 *
 * @version $Id: mo.php 718 2012-10-31 00:32:02Z nbachiyski $
 * @package pomo
 * @subpackage mo
 */

class MO extends Gettext
{
    var $_nplurals = 2;

    /**
     * Fills up with the entries from MO file $filename
     *
     * @param string $filename MO file to load
     *
     * @return bool|mix
     */
    function import_from_file($filename)
    {
        $reader = new FileReader($filename);
        if (!$reader->is_resource())
            return FALSE;
        return $this->import_from_reader($reader);
    }

    function export_to_file($filename)
    {
        $fh = fopen($filename, 'wb');
        if (!$fh) return FALSE;
        $res = $this->export_to_file_handle($fh);
        fclose($fh);
        return $res;
    }

    function export()
    {
        $tmp_fh = fopen("php://temp", 'r+');
        if (!$tmp_fh) return FALSE;
        $this->export_to_file_handle($tmp_fh);
        rewind($tmp_fh);
        return stream_get_contents($tmp_fh);
    }

    function is_entry_good_for_export($entry)
    {
        if (empty($entry->translations)) {
            return FALSE;
        }

        if (!array_filter($entry->translations)) {
            return FALSE;
        }

        return TRUE;
    }

    function export_to_file_handle($fh)
    {
        $entries = array_filter($this->entries, array($this, 'is_entry_good_for_export'));
        ksort($entries);
        $magic                     = 0x950412de;
        $revision                  = 0;
        $total                     = count($entries) + 1; // all the headers are one entry
        $originals_lenghts_addr    = 28;
        $translations_lenghts_addr = $originals_lenghts_addr + 8 * $total;
        $size_of_hash              = 0;
        $hash_addr                 = $translations_lenghts_addr + 8 * $total;
        $current_addr              = $hash_addr;
        fwrite($fh, pack('V*', $magic, $revision, $total, $originals_lenghts_addr,
            $translations_lenghts_addr, $size_of_hash, $hash_addr));
        fseek($fh, $originals_lenghts_addr);

        // headers' msgid is an empty string
        fwrite($fh, pack('VV', 0, $current_addr));
        $current_addr++;
        $originals_table = chr(0);

        foreach ($entries as $entry) {
            $originals_table .= $this->export_original($entry) . chr(0);
            $length = strlen($this->export_original($entry));
            fwrite($fh, pack('VV', $length, $current_addr));
            $current_addr += $length + 1; // account for the NULL byte after
        }

        $exported_headers = $this->export_headers();
        fwrite($fh, pack('VV', strlen($exported_headers), $current_addr));
        $current_addr += strlen($exported_headers) + 1;
        $translations_table = $exported_headers . chr(0);

        foreach ($entries as $entry) {
            $translations_table .= $this->export_translations($entry) . chr(0);
            $length = strlen($this->export_translations($entry));
            fwrite($fh, pack('VV', $length, $current_addr));
            $current_addr += $length + 1;
        }

        fwrite($fh, $originals_table);
        fwrite($fh, $translations_table);
        return TRUE;
    }

    function export_original($entry)
    {
        //TODO: warnings for control characters
        $exported = $entry->singular;
        if ($entry->is_plural) $exported .= chr(0) . $entry->plural;
        if (!is_null($entry->context)) $exported = $entry->context . chr(4) . $exported;
        return $exported;
    }

    function export_translations($entry)
    {
        //TODO: warnings for control characters
        return implode(chr(0), $entry->translations);
    }

    function export_headers()
    {
        $exported = '';
        foreach ($this->headers as $header => $value) {
            $exported .= "$header: $value\n";
        }
        return $exported;
    }

    function get_byteorder($magic)
    {
        // The magic is 0x950412de

        // bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
        $magic_little    = (int)-1794895138;
        $magic_little_64 = (int)2500072158;
        // 0xde120495
        $magic_big = ((int)-569244523) & 0xFFFFFFFF;
        if ($magic_little == $magic || $magic_little_64 == $magic) {
            return 'little';
        } else if ($magic_big == $magic) {
            return 'big';
        } else {
            return FALSE;
        }
    }

    function import_from_reader($reader)
    {
        $endian_string = MO::get_byteorder($reader->readint32());
        if (FALSE === $endian_string) {
            return FALSE;
        }
        $reader->setEndian($endian_string);

        $endian = ('big' == $endian_string) ? 'N' : 'V';

        $header = $reader->read(24);
        if ($reader->strlen($header) != 24)
            return FALSE;

        // parse header
        $header = unpack("{$endian}revision/{$endian}total/{$endian}originals_lenghts_addr/{$endian}translations_lenghts_addr/{$endian}hash_length/{$endian}hash_addr", $header);
        if (!is_array($header))
            return FALSE;

        extract($header);

        // support revision 0 of MO format specs, only
        if ($revision != 0)
            return FALSE;

        // seek to data blocks
        $reader->seekto($originals_lenghts_addr);

        // read originals' indices
        $originals_lengths_length = $translations_lenghts_addr - $originals_lenghts_addr;
        if ($originals_lengths_length != $total * 8)
            return FALSE;

        $originals = $reader->read($originals_lengths_length);
        if ($reader->strlen($originals) != $originals_lengths_length)
            return FALSE;

        // read translations' indices
        $translations_lenghts_length = $hash_addr - $translations_lenghts_addr;
        if ($translations_lenghts_length != $total * 8)
            return FALSE;

        $translations = $reader->read($translations_lenghts_length);
        if ($reader->strlen($translations) != $translations_lenghts_length)
            return FALSE;

        // transform raw data into set of indices
        $originals    = $reader->str_split($originals, 8);
        $translations = $reader->str_split($translations, 8);

        // skip hash table
        $strings_addr = $hash_addr + $hash_length * 4;

        $reader->seekto($strings_addr);

        $strings = $reader->read_all();
        $reader->close();

        for ($i = 0; $i < $total; $i++) {
            $o = unpack("{$endian}length/{$endian}pos", $originals[$i]);
            $t = unpack("{$endian}length/{$endian}pos", $translations[$i]);
            if (!$o || !$t) return FALSE;

            // adjust offset due to reading strings to separate space before
            $o['pos'] -= $strings_addr;
            $t['pos'] -= $strings_addr;

            $original    = $reader->substr($strings, $o['pos'], $o['length']);
            $translation = $reader->substr($strings, $t['pos'], $t['length']);

            if ('' === $original) {
                $this->set_headers($this->make_headers($translation));
            } else {
                $entry                        = & $this->make_entry($original, $translation);
                $this->entries[$entry->key()] = & $entry;
            }
        }
        return TRUE;
    }

    /**
     * Build a Translation_Entry from original string and translation strings,
     * found in a MO file
     *
     * @static
     *
     * @param string $original    original string to translate from MO file. Might contain
     *                            0x04 as context separator or 0x00 as singular/plural separator
     * @param string $translation translation string from MO file. Might contain
     *                            0x00 as a plural translations separator
     */
    function &make_entry($original, $translation)
    {
        $entry = new Entry();
        // look for context
        $parts = explode(chr(4), $original);
        if (isset($parts[1])) {
            $original       = $parts[1];
            $entry->context = $parts[0];
        }
        // look for plural original
        $parts           = explode(chr(0), $original);
        $entry->singular = $parts[0];
        if (isset($parts[1])) {
            $entry->is_plural = TRUE;
            $entry->plural    = $parts[1];
        }
        // plural translations are also separated by \0
        $entry->translations = explode(chr(0), $translation);
        return $entry;
    }

    function select_plural_form($count)
    {
        return $this->gettext_select_plural_form($count);
    }

    function get_plural_forms_count()
    {
        return $this->_nplurals;
    }
}