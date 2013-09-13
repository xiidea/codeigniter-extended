<?php
/**
 * @Author: Roni Kumar Saha
 *        Date: 9/6/13
 *        Time: 8:01 PM
 */

namespace Xiidea\Installer\Services;


class Colors
{
    private static $foreground_colors = array(
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'darkgray'     => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'lightblue'    => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'lightgreen'   => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'lightcyan'    => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'lightred'     => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'lightpurple'  => '1;35',
        'brown'        => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'lightgray'    => '0;37',
        'white'        => '1;37'
    );

    private static $background_colors = array(
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
        'lightgray'  => '47'
    );

    private static $bg_alternat = array(
        'black'      => 'white',
        'red'        => 'white',
        'green'      => 'white',
        'yellow'     => 'black',
        'blue'       => 'white',
        'magenta'    => 'white',
        'cyan'       => 'white',
        'light_gray' => 'white',
        'lightgray'  => 'white'
    );

    private static $hasColorSupport = null;

    // Returns colored string
    public static function getColoredString($string, $foreground_color = NULL, $background_color = NULL)
    {
        if(!self::hasColorSupport()){
            return $string;
        }

        $colored_string = "";

        // Check if given foreground color found
        if (isset(self::$foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset(self::$background_colors[$background_color])) {
            $colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    }

    public static function  warning($msg)
    {
        return self::color($msg, "red");
    }

    public static function  error($msg)
    {
        return self::bgColor('red', $msg);
    }

    public static function  confirm($msg)
    {
        return self::bgColor('red', $msg);
    }

    public static function  success($msg)
    {
        return self::bgColor('green', $msg);
    }

    public static function  message($msg)
    {
        return self::color($msg, "green");
    }

    public static function  info($msg)
    {
        return self::color($msg, "brown");
    }

    public static function  highlight($msg)
    {
        return self::bgColor("magenta", $msg);
    }

    public static function bgColor($color, $msg)
    {
        return self::color($msg, self::$bg_alternat[$color], $color);
    }

    public static function color($msg, $color = NULL, $bg = NULL)
    {
        return self::getColoredString($msg, $color, $bg);
    }

    public static function __callStatic($method, $args)
    {
        $colorAsMethod = strtolower($method);


        if (stristr($method, 'bg')) {

            $color = substr($colorAsMethod, 2);

            if (isset(self::$background_colors[$color])) {
                return self::bgColor($color, $args[0]);
            }

        } elseif (isset(self::$foreground_colors[$colorAsMethod])) {

            $bgColor = isset($args[1]) ? $args[1] : NULL;

            return self::color($args[0], $colorAsMethod, $bgColor);

        }

        $trace = debug_backtrace();

        trigger_error(
            'Undefined function : ' . $method .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);

        return NULL;
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     *  -  windows without ansicon and ConEmu
     *  -  non tty consoles
     *
     * @return Boolean true if the stream supports colorization, false otherwise
     */
    private static function hasColorSupport()
    {
        if(null !== self::$hasColorSupport){
            return self::$hasColorSupport;
        }

        // @codeCoverageIgnoreStart
        if (DIRECTORY_SEPARATOR == '\\') {
            self::$hasColorSupport = FALSE !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
            return self::$hasColorSupport;
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
        // @codeCoverageIgnoreEnd
    }

}
 