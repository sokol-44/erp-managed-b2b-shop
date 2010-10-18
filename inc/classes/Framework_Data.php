<?php
/**
 * Framework_Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michal Sokolowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class Framework_Data extends HTML {

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

   function show_hotels_list($selected_in = '', $tochose = false, $parameters_in = array()) {
      $hotels = Data::get_hotels_list();
      $Lang = Lang::g_global();

      $parameters = self::__prepare_params($parameters_in);
      $selected =  self::__prepare_selected($selected_in);

      if( $tochose ) {
         //TODO it is right method to return no answer ?
         $data[0] = array(
         	'id' => '', //maybe null
         	'text' => $Lang->_('CHOSE ONE'));
      }

      foreach($hotels as $hotel) {
         $data[] = array(
         	'id' => $hotel['id_hotel'],
         	'text' => $this->output_string($hotel['name'] . ' - ' . $hotel['description']) );
      }

      return $this->draw_pull_down_menu('id_hotel', $data, $selected, $parameters);

   }

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
?>
