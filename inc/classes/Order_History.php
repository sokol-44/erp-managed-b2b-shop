<?php
/**
 * Order.php
 * Copyright Michał Sokołowski 2013
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Order_History
 *
 * Data class going to provide data and operations on order history.
 * Application will get and save any data through it.
 *
 * @designPattern Entity List
 *
 * @todo Add strict types declaration to the file.
 * @todo Rename class to follow PSR-12 CamelCase naming conventions (e.g., OrderHistory).
 * @todo Define explicit visibility modifiers (public, protected, private) for all methods.
 * @todo Implement proper Dependency Injection instead of relying on global state and static singletons.
 */
class Order_History {
    /**
     * @var Order_History|null Holds the singleton instance of the class.
     */
    static $class;

    /**
     * @var array List of order history items.
     */
    static public $order_history_list = array();

    /**
     * Order_History constructor.
     *
     * Initializes the instance, registers it to the static class property, and loads data.
     *
     * @return Order_History
     * @todo Remove return statement from the constructor as constructors should not return values in PHP.
     * @todo Add explicit public visibility modifier.
     */
    function __construct() {
        self::$class = $this;

        $this->load_data();

        return self::$class;
    }

    /**
     * Gets the global singleton instance of the class.
     *
     * @return Order_History The singleton instance.
     *
     * @todo Add explicit public static visibility modifiers and return type hint.
     */
    static function g_global() {
        if(self::$class == false) {
            self::$class = new Order_History;
        }
        return self::$class;
    }

    /**
     * Wakeup magic method to restore the singleton instance during unserialization.
     *
     * @return void
     *
     * @todo Add explicit public visibility modifier and void return type hint.
     */
    function __wakeup() {
        self::$class = $this;
    }

    /**
     * Finds the ID associated with a given text in the order history list.
     *
     * @param string $text The text to search for.
     * @return int|string|false The ID of the text if found, or false otherwise.
     *
     * @todo Add explicit public static visibility modifiers, parameter type hints, and return type hint.
     */
    static function text2id($text) {
        self::load_data();

        return array_search($text, self::$order_history_list);
    }

    /**
     * Finds the text associated with a given ID in the order history list.
     *
     * @param int|string $id The ID to search for.
     * @return string|false The text if found, or false otherwise.
     *
     * @todo Add explicit public static visibility modifiers, parameter type hints, and return type hint.
     */
    static function id2text($id) {
        self::load_data();

        if( isset(self::$order_history_list[$id]) ) return self::$order_history_list[$id];
        return false;
    }

    /**
     * Loads the order history data from the framework data source.
     *
     * @return void
     *
     * @todo Remove the unused local variable $F.
     * @todo Add explicit public static visibility modifiers and void return type hint.
     */
    static function load_data( ) {
        $F = Framework::g_global();

        if( Framework::is_null(self::$order_history_list) ) {
            self::$order_history_list = Data::get_order_history_list();
        }
    }

}
