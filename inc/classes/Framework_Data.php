<?php
/**
 * Framework_Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michal Sokolowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class Framework_Data
 *
 * Provides helper methods to generate HTML select and pull-down menus for various data lists.
 * Extends the HTML helper class to render UI components.
 *
 * @todo Add visibility modifiers (public, protected, private) to all methods.
 * @todo Add strict type declarations (declare(strict_types=1);) at the top of the file.
 * @todo Add type hinting for method parameters and return types.
 * @todo Replace old array() syntax with modern short array syntax [].
 * @todo Refactor static calls to Data and Lang classes to use dependency injection.
 */
class Framework_Data extends HTML {

   /**
    * Generates and returns an HTML select menu for rights list based on the provided type.
    *
    * @param mixed $type The type of rights to retrieve.
    * @param mixed $selected_in The selected rights identifier(s). Defaults to ''.
    * @return string The HTML select menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Replace array() with short array syntax [].
    */
   function show_rights_list($type, $selected_in = '') {
      $rights = Data::get_rights_list($type);

      $selected =  self::__prepare_selected($selected_in);

      if( sizeof($selected) > 0 ) {
         $data = array();
      } else {
         $data[] = array('id' => 0,
             'text' => Lang::_('Chose one') );
      }


      foreach($rights as $right) {
         $data[] = array(
             'id' => $right['id_rights'],
             'text' => $this->output_string($right['name'] . ' - ' . $right['description']) );
      }

      return $this->draw_select_menu('rights_ids', $data, $selected, array(), 4);
   }

   /**
    * Retrieves all pictures grouped by hotel ID.
    *
    * @param mixed $owner_id_in Optional owner ID (currently unused in the method logic). Defaults to ''.
    * @return array An associative array of pictures grouped by hotel ID.
    *
    * @todo Add public visibility modifier, type hinting, and remove unused $owner_id_in parameter.
    * @todo Replace array() with short array syntax [].
    */
   function get_all_pictures_by_hotel($owner_id_in = '') {
      $pictures_array = Data::get_all_pictures();

      $pictures_out_array = array();
      if( is_array($pictures_array) ) {
         foreach($pictures_array as $picture_data) {
            if( !isset($pictures_out_array['id_hotel_' . $picture_data['id_hotel']]) ) {
               $pictures_out_array['id_hotel_' . $picture_data['id_hotel']] = array();
            }
            $pictures_out_array['id_hotel_' . $picture_data['id_hotel']][] = $picture_data;
         }
      }

      return $pictures_out_array;
   }



   /**
    * Generates and returns an HTML pull-down menu for currencies.
    *
    * @param mixed $selected_in The selected currency identifier. Defaults to ''.
    * @param array $parameters_in Additional HTML attributes/parameters for the select element. Defaults to array().
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Replace array() with short array syntax [].
    */
   function show_currences_list($selected_in = '', $parameters_in = array()) {
      $Lang = Lang::g_global();

      $currences_list[] = array( 'id' => '', 'text' => $Lang->_('CHOSE ONE'));

      $currences_data_list = Data::get_currences_list();

      foreach($currences_data_list as $currency) {
         $currences_list[] = array(
             'id' => $currency['id_currency'],
             'text' => $this->output_string($currency['currency_symbol'] . ' (' . $currency['currency_txt'] . ')') );
      }

      $parameters = self::__prepare_params($parameters_in);
      $selected =  self::__prepare_selected($selected_in);

      return $this->draw_pull_down_menu('type', $currences_list, $selected, $parameters);
   }


   /**
    * Generates and returns an HTML pull-down menu for paid types.
    *
    * @param mixed $selected_in The selected paid type identifier. Defaults to ''.
    * @param array $parameters_in Additional HTML attributes/parameters for the select element. Defaults to array().
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Replace array() with short array syntax [].
    */
   function show_paid_type_list($selected_in = '', $parameters_in = array()) {
      $Lang = Lang::g_global();

      $paid_type_list[] = array( 'id' => '', 'text' => $Lang->_('CHOSE ONE'));

      $paid_type_data_list = Data::get_pattern_special_paid_types_list();

      foreach($paid_type_data_list as $key => $text) {
         $paid_type_list[] = array(
             'id' => $key,
             'text' => $this->output_string($Lang->_($text)) );
      }

      $parameters = self::__prepare_params($parameters_in);
      $selected =  self::__prepare_selected($selected_in);

      return $this->draw_pull_down_menu('repeat_type', $paid_type_list, $selected, $parameters);
   }


   /**
    * Generates and returns an HTML pull-down menu for attribute types.
    *
    * @param mixed $selected_in The selected attribute type identifier. Defaults to ''.
    * @param array $parameters_in Additional HTML attributes/parameters for the select element. Defaults to array().
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Replace array() with short array syntax [].
    */
   function show_attrib_type_list($selected_in = '', $parameters_in = array()) {
      $Lang = Lang::g_global();

      $attrib_type_list[] = array( 'id' => '', 'text' => $Lang->_('CHOSE ONE'));

      $attrib_type_data_list = Data::get_pattern_special_attrib_types_list();

      foreach($attrib_type_data_list as $key => $text) {
         $attrib_type_list[] = array(
             'id' => $key,
             'text' => $this->output_string($Lang->_($text)) );
      }

      $parameters = self::__prepare_params($parameters_in);
      $selected =  self::__prepare_selected($selected_in);

      return $this->draw_pull_down_menu('type', $attrib_type_list, $selected, $parameters);
   }

   /**
    * Generates and returns an HTML pull-down menu for clients.
    *
    * @param mixed $selected_in The selected client identifier. Defaults to ''.
    * @param bool $tochose Whether to include a default "CHOSE ONE" option. Defaults to false.
    * @param array $parameters_in Additional HTML attributes/parameters for the select element. Defaults to array().
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Rename parameter $tochose to $toChoose to follow camelCase naming standards.
    * @todo Replace array() with short array syntax [].
    */
   function show_clients_list($selected_in = '', $tochose = false, $parameters_in = array()) {
      $clients = Data::get_client_list();
      $Lang = Lang::g_global();

      $parameters = self::__prepare_params($parameters_in);
      $selected =  self::__prepare_selected($selected_in);

      if( $tochose ) {
         //TODO it is right method to return no answer ?
         $data[0] = array(
             'id' => '', //maybe null
             'text' => $Lang->_('CHOSE ONE'));
      }

      foreach($clients as $client) {
         $data[] = array(
             'id' => $client['id_client'],
             'text' => $this->output_string($client['name'] . ' - ' . $client['description']) );
      }

      return $this->draw_pull_down_menu('id_client', $data, $selected, $parameters);

   }

   /**
    * Generates and returns an empty HTML pull-down menu, optionally with a default "CHOSE ONE" option.
    *
    * @param string $name The name attribute of the select element. Defaults to ''.
    * @param bool $tochose Whether to include a default "CHOSE ONE" option. Defaults to false.
    * @param array $parameters Additional HTML attributes/parameters for the select element. Defaults to array().
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Rename parameter $tochose to $toChoose to follow camelCase naming standards.
    * @todo Replace array() with short array syntax [].
    */
   function show_empty_list($name = '', $tochose = false, $parameters = array()) {
      $Lang = Lang::g_global();
      $data = array();

      if( $tochose ) {
         //TODO it is right method to return no answer ?
         $data[] = array(
             'id' => '', //maybe null
             'text' => $Lang->_('CHOSE ONE'));
      }

      return $this->draw_pull_down_menu($name, $data, array(), $parameters);
   }

   /**
    * Generates and returns an HTML pull-down menu for person account states.
    *
    * @param mixed $type The type of person account state to retrieve.
    * @param mixed $selected_in The selected state identifier. Defaults to ''.
    * @return string The HTML pull-down menu representation.
    *
    * @todo Add public visibility modifier, type hinting for parameters, and string return type.
    * @todo Replace array() with short array syntax [].
    */
   function show_person_account_state($type, $selected_in = '') {
      $states = Data::get_person_account_state($type);

      foreach($states as $state) {
         $data[] = array(
             'id' => $state['name'],
             'text' => $state['description'] );
      }

      $selected =  self::__prepare_selected($selected_in);

      return $this->draw_pull_down_menu('state', $data, $selected);
   }

}
