<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Data_Contact
 *
 * Data class going to provide data and operation on them.
 * Application will get and save any data through it.
 *
 * @todo Implement PSR-4 namespacing and rename class to follow PSR-12 standards (e.g., Data\Contact).
 * @todo Add declare(strict_types=1) to the top of the file to enforce strict typing.
 */
class Data_Contact {

    /**
     * Data_Contact constructor.
     *
     * Initializes the Data_Contact class instance.
     *
     * @return void
     *
     * @todo Add explicit public visibility modifier to the constructor.
     */
    function __construct() {
        // echo get_class();
        //parent::__construct();
    }

}
