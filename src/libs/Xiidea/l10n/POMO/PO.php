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

define('PO_MAX_LINE_LEN', 79);

ini_set('auto_detect_line_endings', 1);

/**
 * Class for working with PO files
 *
 * @version $Id: po.php 718 2012-10-31 00:32:02Z nbachiyski $
 * @package pomo
 * @subpackage po
 */
class PO extends Gettext
{

    var $comments_before_headers = '';

    /**
     * Exports headers to a PO entry
     *
     * @return string msgid/msgstr PO entry for this PO file headers, doesn't contain newline at the end
     */
    function export_headers()
    {
        $header_string = '';
        foreach ($this->headers as $header => $value) {
            $header_string .= "$header: $value\n";
        }
        $poified = PO::poify($header_string);
        if ($this->comments_before_headers)
            $before_headers = $this->prepend_each_line(rtrim($this->comments_before_headers) . "\n", '# ');
        else
            $before_headers = '';
        return rtrim("{$before_headers}msgid \"\"\nmsgstr $poified");
    }

    /**
     * Exports all entries to PO format
     *
     * @return string sequence of mgsgid/msgstr PO strings, doesn't containt newline at the end
     */
    function export_entries()
    {
        //TODO sorting
        return implode("\n\n", array_map(array('PO', 'export_entry'), $this->entries));
    }

    /**
     * Exports the whole PO file as a string
     *
     * @param bool $include_headers whether to include the headers in the export
     *
     * @return string ready for inclusion in PO file string for headers and all the enrtries
     */
    function export($include_headers = TRUE)
    {
        $res = '';
        if ($include_headers) {
            $res .= $this->export_headers();
            $res .= "\n\n";
        }
        $res .= $this->export_entries();
        return $res;
    }

    /**
     * Same as {@link export}, but writes the result to a file
     *
     * @param string $filename        where to write the PO string
     * @param bool   $include_headers whether to include tje headers in the export
     *
     * @return bool true on success, false on error
     */
    function export_to_file($filename, $include_headers = TRUE)
    {
        $fh = fopen($filename, 'w');
        if (FALSE === $fh) return FALSE;
        $export = $this->export($include_headers);
        $res    = fwrite($fh, $export);
        if (FALSE === $res) return FALSE;
        return fclose($fh);
    }

    /**
     * Text to include as a comment before the start of the PO contents
     *
     * Doesn't need to include # in the beginning of lines, these are added automatically
     */
    function set_comment_before_headers($text)
    {
        $this->comments_before_headers = $text;
    }

    /**
     * Formats a string in PO-style
     *
     * @static
     *
     * @param string $string the string to format
     *
     * @return string the poified string
     */
    function poify($string)
    {
        $quote   = '"';
        $slash   = '\\';
        $newline = "\n";

        $replaces = array(
            "$slash" => "$slash$slash",
            "$quote" => "$slash$quote",
            "\t"     => '\t',
        );

        $string = str_replace(array_keys($replaces), array_values($replaces), $string);

        $po = $quote . implode("${slash}n$quote$newline$quote", explode($newline, $string)) . $quote;
        // add empty string on first line for readbility
        if (FALSE !== strpos($string, $newline) &&
            (substr_count($string, $newline) > 1 || !($newline === substr($string, -strlen($newline))))
        ) {
            $po = "$quote$quote$newline$po";
        }
        // remove empty strings
        $po = str_replace("$newline$quote$quote", '', $po);
        return $po;
    }

    /**
     * Gives back the original string from a PO-formatted string
     *
     * @static
     *
     * @param string $string PO-formatted string
     *
     * @return string enascaped string
     */
    function unpoify($string)
    {
        $escapes               = array('t' => "\t", 'n' => "\n", '\\' => '\\');
        $lines                 = array_map('trim', explode("\n", $string));
        $lines                 = array_map(array('PO', 'trim_quotes'), $lines);
        $unpoified             = '';
        $previous_is_backslash = FALSE;
        foreach ($lines as $line) {
            preg_match_all('/./u', $line, $chars);
            $chars = $chars[0];
            foreach ($chars as $char) {
                if (!$previous_is_backslash) {
                    if ('\\' == $char)
                        $previous_is_backslash = TRUE;
                    else
                        $unpoified .= $char;
                } else {
                    $previous_is_backslash = FALSE;
                    $unpoified .= isset($escapes[$char]) ? $escapes[$char] : $char;
                }
            }
        }
        return $unpoified;
    }

    /**
     * Inserts $with in the beginning of every new line of $string and
     * returns the modified string
     *
     * @static
     *
     * @param string $string prepend lines in this string
     * @param string $with   prepend lines with this string
     *
     * @return string
     */
    function prepend_each_line($string, $with)
    {
        $php_with = var_export($with, TRUE);
        $lines    = explode("\n", $string);
        // do not prepend the string on the last empty line, artefact by explode
        if ("\n" == substr($string, -1)) unset($lines[count($lines) - 1]);
        $res = implode("\n", array_map(create_function('$x', "return $php_with.\$x;"), $lines));
        // give back the empty line, we ignored above
        if ("\n" == substr($string, -1)) $res .= "\n";
        return $res;
    }

    /**
     * Prepare a text as a comment -- wraps the lines and prepends #
     * and a special character to each line
     *
     * @access private
     *
     * @param string $text the comment text
     * @param string $char character to denote a special PO comment,
     *                     like :, default is a space
     *
     * @return string
     */
    function comment_block($text, $char = ' ')
    {
        $text = wordwrap($text, PO_MAX_LINE_LEN - 3);
        return PO::prepend_each_line($text, "#$char ");
    }

    /**
     * Builds a string from the entry for inclusion in PO file
     *
     * @static
     *
     * @param object &$entry the entry to convert to po string
     *
     * @return string|bool PO-style formatted string for the entry or
     *    false if the entry is empty
     */
    function export_entry(&$entry)
    {
        if (is_null($entry->singular)) return FALSE;
        $po = array();
        if (!empty($entry->translator_comments)) $po[] = PO::comment_block($entry->translator_comments);
        if (!empty($entry->extracted_comments)) $po[] = PO::comment_block($entry->extracted_comments, '.');
        if (!empty($entry->references)) $po[] = PO::comment_block(implode(' ', $entry->references), ':');
        if (!empty($entry->flags)) $po[] = PO::comment_block(implode(", ", $entry->flags), ',');
        if (!is_null($entry->context)) $po[] = 'msgctxt ' . PO::poify($entry->context);
        $po[] = 'msgid ' . PO::poify($entry->singular);
        if (!$entry->is_plural) {
            $translation = empty($entry->translations) ? '' : $entry->translations[0];
            $po[]        = 'msgstr ' . PO::poify($translation);
        } else {
            $po[]         = 'msgid_plural ' . PO::poify($entry->plural);
            $translations = empty($entry->translations) ? array('', '') : $entry->translations;
            foreach ($translations as $i => $translation) {
                $po[] = "msgstr[$i] " . PO::poify($translation);
            }
        }
        return implode("\n", $po);
    }

    function import_from_file($filename)
    {
        $f = fopen($filename, 'r');
        if (!$f) return FALSE;
        $lineno = 0;
        while (TRUE) {
            $res = $this->read_entry($f, $lineno);
            if (!$res) break;
            if ($res['entry']->singular == '') {
                $this->set_headers($this->make_headers($res['entry']->translations[0]));
            } else {
                $this->add_entry($res['entry']);
            }
        }
        PO::read_line($f, 'clear');
        if (FALSE === $res) {
            return FALSE;
        }
        if (!$this->headers && !$this->entries) {
            return FALSE;
        }
        return TRUE;
    }

    function read_entry($f, $lineno = 0)
    {
        $entry = new Entry();
        // where were we in the last step
        // can be: comment, msgctxt, msgid, msgid_plural, msgstr, msgstr_plural
        $context      = '';
        $msgstr_index = 0;
        $is_final     = create_function('$context', 'return $context == "msgstr" || $context == "msgstr_plural";');
        while (TRUE) {
            $lineno++;
            $line = PO::read_line($f);
            if (!$line) {
                if (feof($f)) {
                    if ($is_final($context))
                        break;
                    elseif (!$context) // we haven't read a line and eof came
                        return NULL;
                    else
                        return FALSE;
                } else {
                    return FALSE;
                }
            }
            if ($line == "\n") continue;
            $line = trim($line);
            if (preg_match('/^#/', $line, $m)) {
                // the comment is the start of a new entry
                if ($is_final($context)) {
                    PO::read_line($f, 'put-back');
                    $lineno--;
                    break;
                }
                // comments have to be at the beginning
                if ($context && $context != 'comment') {
                    return FALSE;
                }
                // add comment
                $this->add_comment_to_entry($entry, $line);;
            } elseif (preg_match('/^msgctxt\s+(".*")/', $line, $m)) {
                if ($is_final($context)) {
                    PO::read_line($f, 'put-back');
                    $lineno--;
                    break;
                }
                if ($context && $context != 'comment') {
                    return FALSE;
                }
                $context = 'msgctxt';
                $entry->context .= PO::unpoify($m[1]);
            } elseif (preg_match('/^msgid\s+(".*")/', $line, $m)) {
                if ($is_final($context)) {
                    PO::read_line($f, 'put-back');
                    $lineno--;
                    break;
                }
                if ($context && $context != 'msgctxt' && $context != 'comment') {
                    return FALSE;
                }
                $context = 'msgid';
                $entry->singular .= PO::unpoify($m[1]);
            } elseif (preg_match('/^msgid_plural\s+(".*")/', $line, $m)) {
                if ($context != 'msgid') {
                    return FALSE;
                }
                $context          = 'msgid_plural';
                $entry->is_plural = TRUE;
                $entry->plural .= PO::unpoify($m[1]);
            } elseif (preg_match('/^msgstr\s+(".*")/', $line, $m)) {
                if ($context != 'msgid') {
                    return FALSE;
                }
                $context             = 'msgstr';
                $entry->translations = array(PO::unpoify($m[1]));
            } elseif (preg_match('/^msgstr\[(\d+)\]\s+(".*")/', $line, $m)) {
                if ($context != 'msgid_plural' && $context != 'msgstr_plural') {
                    return FALSE;
                }
                $context                    = 'msgstr_plural';
                $msgstr_index               = $m[1];
                $entry->translations[$m[1]] = PO::unpoify($m[2]);
            } elseif (preg_match('/^".*"$/', $line)) {
                $unpoified = PO::unpoify($line);
                switch ($context) {
                    case 'msgid':
                        $entry->singular .= $unpoified;
                        break;
                    case 'msgctxt':
                        $entry->context .= $unpoified;
                        break;
                    case 'msgid_plural':
                        $entry->plural .= $unpoified;
                        break;
                    case 'msgstr':
                        $entry->translations[0] .= $unpoified;
                        break;
                    case 'msgstr_plural':
                        $entry->translations[$msgstr_index] .= $unpoified;
                        break;
                    default:
                        return FALSE;
                }
            } else {
                return FALSE;
            }
        }
        if (array() == array_filter($entry->translations, create_function('$t', 'return $t || "0" === $t;'))) {
            $entry->translations = array();
        }
        return array('entry' => $entry, 'lineno' => $lineno);
    }

    function read_line($f, $action = 'read')
    {
        static $last_line = '';
        static $use_last_line = FALSE;
        if ('clear' == $action) {
            $last_line = '';
            return TRUE;
        }
        if ('put-back' == $action) {
            $use_last_line = TRUE;
            return TRUE;
        }
        $line          = $use_last_line ? $last_line : fgets($f);
        $line          = ("\r\n" == substr($line, -2)) ? rtrim($line, "\r\n") . "\n" : $line;
        $last_line     = $line;
        $use_last_line = FALSE;
        return $line;
    }

    function add_comment_to_entry(&$entry, $po_comment_line)
    {
        $first_two = substr($po_comment_line, 0, 2);
        $comment   = trim(substr($po_comment_line, 2));
        if ('#:' == $first_two) {
            $entry->references = array_merge($entry->references, preg_split('/\s+/', $comment));
        } elseif ('#.' == $first_two) {
            $entry->extracted_comments = trim($entry->extracted_comments . "\n" . $comment);
        } elseif ('#,' == $first_two) {
            $entry->flags = array_merge($entry->flags, preg_split('/,\s*/', $comment));
        } else {
            $entry->translator_comments = trim($entry->translator_comments . "\n" . $comment);
        }
    }

    function trim_quotes($s)
    {
        if (substr($s, 0, 1) == '"') $s = substr($s, 1);
        if (substr($s, -1, 1) == '"') $s = substr($s, 0, -1);
        return $s;
    }
}
