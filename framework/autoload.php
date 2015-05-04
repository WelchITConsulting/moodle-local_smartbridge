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
 * Filename : autoload
 * Author   : John Welch <jwelch@welchitconsulting.co.uk>
 * Created  : 26 Apr 2015
 */

class smartsbridge_autoload
{
    protected static $instance;
    protected $load;

    public function __construct( $namespace = 'local/smartsbridge/framework' )
    {
        if ( empty( $namespace ) ) {
            throw new coding_exception( 'Cannot autoload with an empty namespace. This will enable autoload for all of Moodle.' );
        }
        $this->load = new smartsbridge_helper_load();
        $this->load->_set_helper_namespace( $namespace );
    }

    public static function get_instance()
    {
        if ( ! self::$instance instanceof smartsbridge_autoload ) {
            self::$instance = new smartsbridge_autoload();
        }
        return self::$instance;
    }

    public function autoload( $class )
    {
        if ( ( strpos( $class, 'smartsbridge_' ) !== 0 ) &&
                ( $this->load->get_namespace() == 'local/smartsbridge/framework') ) {
            return false;
        }
        try {
            $path = str_replace( array( '_', 'block/' ), array('/', 'blocks/'), $class );
            $this->load->file( $path );
        } catch (coding_exception $e) {
            return false;
        }
        return class_exists( $class, false);
    }

    public static function register( smartsbridge_autoload $autoload = NULL )
    {
        if ( is_null( $autoload ) ) {
            $autoload = self::get_instance();
        }
        spl_autoload_register( array( $autoload, 'autoload' ) );
    }

    public static function unregister( smartsbridge_autoload $autoload = NULL )
    {
        if ( is_null( $autoload ) ) {
            $autoload = self::get_instance();
        }
        spl_autoload_unregister( array( $autoload, 'autoload' ) );
    }
}
