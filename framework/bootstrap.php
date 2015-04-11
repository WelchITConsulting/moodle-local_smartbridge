<?php
/*
 * Copyright (C) 2015 Welch IT Consulting
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Filename : bootstrap
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 25 Mar 2015
 */


class smartsbridge_bootstrap
{
    protected static $init = false;
    protected static $zend = false;

    public static function startup()
    {
        if (!self::$init) {
            sb_autoload::register();
            self::$init  = true;
        }
    }

    public static function shutdown()
    {
        if (self::$init) {
            mr_autoload::unregister();
            self::$init = false;
        }
    }

    public static function zend()
    {
        global $CFG;
        if (!self::$zend) {
            $includepath = get_include_path();
            $searchpath  = $CFG->dirroot . '/search';
            $zendpath    = $CFG->dirroot . '/zend';
            $zendsbpath  = $CFG->dirroot . '/local/sb/zend';
            $paths       = array($zendsbpath, $zendpath, $searchpath);

            if (is_dir($searchpath) || is_dir($zendsbpath)) {
                $includepaths = explode(PATH_SEPARATOR, $includepath);
                foreach($includepaths as $key => $path) {
                    if (  in_array($path, $paths)) {
                        unset($includepaths[$key]);
                    }
                }
                foreach($paths as $path) {
                    if (is_dir($path)) {
                        array_unshift($includepaths, $path);
                    }
                }
                set_include_path(implode(PATH_SEPARATOR, $includepaths));
            }
            self::$zend = true;
        }
    }

    public static function redis()
    {
        global $CFG;

        if (!class_exists('Redis')) {
            throw new Exception('Redis class not found, Redis PHP Extension is probably not installed');
        }
        if (empty($CFG->local_smartsbridge_redis_server)) {
            throw new Exception('Redis connection string is not configured in $CFG->local_sb_redis_server');
        }
        $redis = new Redis();
        $redis->connect($CFG->local_sb_redis_server);
        return $redis;
    }
}
