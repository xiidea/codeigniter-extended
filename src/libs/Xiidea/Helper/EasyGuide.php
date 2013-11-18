<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Helper;

class EasyGuide
{

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */

    public static $routes = array();

    public $temporary_routes = array();
    public $namespace = '';

    private static $restFullActionMap = array(
        'new' => 'create_new',
        'view' => 'show',
        'edit' => 'edit',
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
    );

    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */

    public function __construct($namespace = FALSE)
    {
        if ($namespace)
        {
            $this->namespace = $namespace;
        }
    }

    public static function map($callback)
    {
        $guide = new self();
        call_user_func_array($callback, array( &$guide ));

        self::$routes = $guide->temporary_routes;
    }

    /**
     * @param array $restFullActionMap
     */
    public static function setRestFullActionMap($restFullActionMap)
    {
        self::$restFullActionMap = array_merge(self::$restFullActionMap, $restFullActionMap);
    }

    /* --------------------------------------------------------------
     * BASIC ROUTING
     * ------------------------------------------------------------ */

    public function route($from, $to, $nested = FALSE)
    {
        $parameterfy = FALSE;

        // Allow for array based routes and hashrouters
        if (is_array($to))
        {
            $to = strtolower($to[0]) . '/' . strtolower($to[1]);
            $parameterfy = TRUE;
        }
        elseif (preg_match('/^([a-zA-Z\_\-0-9\/]+)#([a-zA-Z\_\-0-9\/]+)$/m', $to, $matches))
        {
            $to = $matches[1] . '/' . $matches[2];
            $parameterfy = TRUE;
        }

        // Do we have a namespace?
        if ($this->namespace)
        {
            $from = $this->namespace . '/' . $from;
        }

        // Account for parameters in the URL if we need to
        if ($parameterfy)
        {
            $to = $this->parameterfy($from, $to);
        }

        // Apply our routes
        $this->temporary_routes[$from] = $to;

        // Do we have a nesting function?
        if ($nested && is_callable($nested))
        {
            $nested_guide = new self($from);
            call_user_func_array($nested, array( &$nested_guide ));
            $this->temporary_routes = array_merge($this->temporary_routes, $nested_guide->temporary_routes);
        }
    }

    /* --------------------------------------------------------------
     * HTTP VERB ROUTING
     * ------------------------------------------------------------ */

    public function get($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $this->route($from, $to);
        }
    }

    public function post($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->route($from, $to);
        }
    }

    public function put($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT')
        {
            $this->route($from, $to);
        }
    }

    public function delete($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'DELETE')
        {
            $this->route($from, $to);
        }
    }

    public function patch($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PATCH')
        {
            $this->route($from, $to);
        }
    }

    public function head($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'HEAD')
        {
            $this->route($from, $to);
        }
    }

    public function options($from, $to)
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $this->route($from, $to);
        }
    }

    /* --------------------------------------------------------------
     * RESTFUL ROUTING
     * ------------------------------------------------------------ */

    public function resources($name, $nested = FALSE)
    {
        $this->get($name, $name . '#index');
        $this->get($name . '/new', $name . '#'.self::$restFullActionMap['new']);
        $this->get($name . '/([a-zA-Z0-9\-_]+)/edit', $name . '#'.self::$restFullActionMap['edit']);
        $this->get($name . '/edit/([a-zA-Z0-9\-_]+)', $name . '#'.self::$restFullActionMap['edit']);
        $this->get($name . '/([a-zA-Z0-9\-_]+)', $name . '#'.self::$restFullActionMap['view']);
        $this->post($name, $name . '#'.self::$restFullActionMap['create']);
        $this->put($name . '/([a-zA-Z0-9\-_]+)', $name . '#'.self::$restFullActionMap['update']);
        $this->delete($name . '/([a-zA-Z0-9\-_]+)', $name . '#'.self::$restFullActionMap['delete']);

        if ($nested && is_callable($nested))
        {
            $nested_guide = new Self($name . '/([a-zA-Z0-9\-_]+)');
            call_user_func_array($nested, array( &$nested_guide ));
            $this->temporary_routes = array_merge($this->temporary_routes, $nested_guide->temporary_routes);
        }
    }

    public function resource($name, $nested = FALSE)
    {
        $this->get($name, $name . '/'.self::$restFullActionMap['view']);
        $this->get($name . '/edit', $name . '/'.self::$restFullActionMap['edit']);
        $this->post($name, $name . '/'.self::$restFullActionMap['create']);
        $this->put($name, $name . '/'.self::$restFullActionMap['update']);
        $this->delete($name, $name . '/'.self::$restFullActionMap['delete']);

        if ($nested && is_callable($nested))
        {
            $nested_guide = new Self($name);
            call_user_func_array($nested, array( &$nested_guide ));
            $this->temporary_routes = array_merge($this->temporary_routes, $nested_guide->temporary_routes);
        }
    }

    /* --------------------------------------------------------------
     * UTILITY FUNCTIONS
     * ------------------------------------------------------------ */

    /**
     * Clear out the routing table
     */
    public static function clear()
    {
        self::$routes = array();
    }

    /**
     * Return the routes array
     */
    public static function draw()
    {
        return self::$routes;
    }

    /**
     * Extract the URL parameters from $from and copy to $to
     */
    public static function parameterfy($from, $to)
    {
        if (preg_match_all('/\/\((.*?)\)/', $from, $matches))
        {
            $params = '';

            foreach ($matches[1] as $i => $match)
            {
                $i = $i + 1;
                $params .= "/\$$i";
            }

            $to .= $params;
        }

        return $to;
    }
} 