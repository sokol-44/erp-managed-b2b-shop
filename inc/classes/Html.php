<?php
/**
 * HTML.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Micha� Soko�owski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

class HTML {


   private function __create_image_paths($src, $admin = false) {

      if( substr($src, 0, 1) == '/' ) {
         $local_src = str_replace('/', DS, substr($src, 1));
         
         $www_src = URL_FULL . substr($src, 1);
      } else {
         $local_src = str_replace('/', DS, DIR_LOCAL_IMG . $src);
   
         $www_src = DIR_WWW_IMG . $src;
      }

      return array($local_src, $www_src);
   }

   private function __getimagesize($src) {
      $image_size = getimagesize($src);
      if( $image_size ) {
         return array('width' => $image_size[0], 'height' => $image_size[1]);
      } else {
         return false;
      }
   }



   /**
    * Outoput img with parameteres from db
    * @param int/string $id picture id or string
    * @param string $type 'SMALL','NORMAL','ORIGINAL'
    * @param string $alt text for picture
    * @param array $parameters
    */
   function image_db($id, $type = '', $alt = '', $parameters = '') {
      $F = Framework::g_global();

      $pic_attribute = array();
      if( ctype_digit($id) && (int)$id == $id ) {
         $pic_info = Data::get_subpicture_data($id, $type);
         if( $pic_info ) {
            $pic_attribute['width'] = $pic_info['width'];
            $pic_attribute['height'] = $pic_info['height'];
            if( Framework::not_null($alt) ) {
               $pic_attribute['alt'] = $F->output_string($alt);
            } else {
               $pic_attribute['alt'] = $F->output_string($pic_info['name']);
            }
            $pic_attribute['src'] = '/img.php?id=' . (int)$id . '&type=' . $pic_info['type'];
         } else {
            $pic_attribute['src'] = '';
         }
      } else {
         $pic_attribute['src'] = '/img.php?id=' . $F->output_string($id) . '&type=' . $F->output_string($type);
      }
      $attributes = self::__unroll_params($pic_attribute);
      $parameters = self::__unroll_params( self::__prepare_params($parameters) );

      $image = '<img' . $attributes . $parameters . '>';
      return $image;
   }

   // The HTML image wrapper function
   //FIXME - load from DB
   /**
    * @param path $src
    * @param string $alt
    * @param mixed $parameters
    * @param int $width
    * @param int $height
    * @return full image string
    */
   function static_image($src, $alt = '', $parameters = '', $width = '', $height = '') {
      if ( (empty($src) || ($src == DIR_WS_IMAGES)) ) {
         return false;
      }

      list($local_src, $www_src) = self::__create_image_paths($src, true);

      //      echo "#SRC#$local_src#        #$www_src#<br>\r";

      // alt is added to the img tag even if it is null to prevent browsers from outputting
      // the image filename as default
      $image = '<img src="' . $this->output_string($www_src) . '" border="0" alt="' . $this->output_string($alt) . '"';

      if ($this->not_null($alt)) {
         $image .= ' title=" ' . $this->output_string($alt) . ' "';
      }

      if ($image_size = self::__getimagesize($local_src)) {
         if ($this->is_null($width) && $this->not_null($height)) {
            $ratio = $height / $image_size['height'];
            $width = intval($image_size['width'] * $ratio);
         } elseif ($this->not_null($width) && $this->is_null($height)) {
            $ratio = $width / $image_size['width'];
            $height = intval($image_size['height'] * $ratio);
         } elseif ($this->is_null($width) && $this->is_null($height)) {
            $width = $image_size['width'];
            $height = $image_size['height'];
         }
      } elseif (IMAGE_REQUIRED == 'false') {
         return false;
      }

      if ($this->not_null($width) && $this->not_null($height)) {
         $image .= ' width="' . $this->output_string($width) . '" height="' . $this->output_string($height) . '"';
      }

      if ($this->not_null($parameters)) $image .= ' ' . $parameters;

      $image .= '>';

      return $image;
   }


   function put_js_redirect($page) {
      return "<script>location.href='$page';</script>";
   }

   ////
   // The HTML form submit button wrapper function
   // Outputs a button in the selected language
   function static_image_submit($image, $alt = '', $parameters = '') {
      global $language;

      $image_submit = '<input type="image" src="' . $image . '" border="0" alt="' . $this->output_string($alt) . '"';

      if ($this->not_null($alt)) $image_submit .= ' title=" ' . $this->output_string($alt) . ' "';

      if ($this->not_null($parameters)) $image_submit .= ' ' . $parameters;

      $image_submit .= '>';

      return $image_submit;
   }

   ////
   // Output a function button in the selected language
   function static_image_button($image, $alt = '', $parameters = '') {
      global $language;

      return $this->static_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, $parameters, '', '');
   }

   ////
   // Output a separator either through whitespace, or with an image
   function draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
      return $this->image(DIR_WS_IMAGES . $image, '', $width, $height);
   }

   function init_table() {

   }

   function init_rows($name = '') {
      if( $this->is_null($name) ) {
         $name = 'default';
      }
      if( isset($this->{$name}) ) {
         //
      } else {
         $this->{$name} = array();
      }
   }

   function add_row($name = '', $params = '') {
      if( $this->is_null($name) ) {
         $name = 'default';
      }
      if( isset($this->{$name}) ) {
         $this->{$name}[] = array();
      } else {
         $this->{$name} = array();
         $this->{$name}[] = array();
      }

      end($this->{$name});
      $key = key($this->{$name});
      $this->{$name}[$key]['params'] = $params;
   }

   function add_cell($name = '', $content = '', $type = 'td', $params = '') {
      if( $this->is_null($name) ) {
         $name = 'default';
      }
      if( isset($this->{$name}) ) {
         //
      } else {
         return false;
      }

      end($this->{$name});
      $key = key($this->{$name});

      $this->{$name}[$key]['cells'][] = array('type' => $type, 'params' => $params, 'content' => $content);
   }

   function get_rows($name = '') {
      if( $this->is_null($name) ) {
         $name = 'default';
      }
      if( isset($this->{$name}) ) {
         return $this->{$name};
      } else {
         return array();
      }
   }

   static function __unroll_params($params) {
      $param_str = '';
      if( Framework::not_null($params) ) {
         if( is_array($params) ) {
            $tr_parm = array();
            foreach( $params as $attr => $val ) {
               $tr_parm[] = $attr . '="' . $val . '" ';
            }
            return ' ' . implode(' ', $tr_parm);
         } else {
            return ' ' . $params_array;
         }
      } else {
         return $param_str;
      }
   }

   static function __prepare_params($params_in){
      if( is_array($params_in) ) {
         $params = $params_in;
      } else {
         if( Framework::not_null($params_in) ) {
            $params_t = explode(',', $params_in);
            foreach($params_t as $params_eq) {
               $param_rt = explode('=', $params_eq);
               if(isset($param_rt[1])) {
                  $key = trim($param_rt[0]);
                  $val = trim( str_replace(array('"','\''), '', $param_rt[1]) );
               } else {
                  $kv = trim($param_rt[0]);
                  $params[$kv] = $kv;
               }
            }
         } else {
            $params = array();
         }
      }
      return $params;
   }

   static function __prepare_selected($selected_in){
      if( is_array($selected_in) ) {
         $selected = $selected_in;
      } else {
         if( Framework::not_null($selected_in) ) {
            $selected = explode(',', $selected_in);
         } else {
            $selected = array();
         }
      }
      return $selected;
   }

   /**
    * Output table rows
    * @param array $rows_array
    * @param bool $checkbox = true
    * @return string
    */
   function table_rows(array $rows_array, $checkbox = true) {
      $res = '';

      foreach($rows_array as $row) {
         $res .= "\r";

         $tr_params = self::__unroll_params($row['params']);

         $res .= '<tr' . ($this->not_null($tr_params)?' ' . $tr_params:'')  . ">\r";
         if( $checkbox && is_array($row['boxparam'])) {
            if(  !isset($row['cells']['type']) || $row['cells']['type'] != 'th' ) {
               $cell_params = self::__unroll_params($row['boxparam']['params']);
               $res .= '<td' . ($this->not_null($cell_params)?' ' . $cell_params:'') . '>';
               $res .= self::draw_radio_field($row['boxparam']['name'], $row['boxparam']['value']);
               $res .= "</td>\r";
            }
         }
         foreach( $row['cells'] as $cell ) {
            $cell_params = self::__unroll_params($cell['params']);
            if( isset($cell['type']) && $cell['type'] == 'th' ) $cell_type = 'th';
            else $cell_type = 'td';
            $res .= '<' . $cell_type . ($this->not_null($cell_params)?' ' . $cell_params:'') . '>';
            $res .= $cell['content'];
            $res .= '</' . $cell_type . ">\r";
         }
         $res .= "</tr>\r";
      }
      return $res;
   }

   ////
   //
   /**
   * Output a form start
   * (increment count)
   * @param string $name
   * @param string $action
   * @param string $method = 'post'
   * @param string $parameters = ''
   * @return string
   */
   function draw_form($name, $action, $method = 'post', $parameters = '') {

      $form = '<form id="' . $this->output_string($name) . '" name="' . $this->output_string($name) . '" action="' . $action . '" method="' . $this->output_string($method) . '"';
      if ($this->not_null($parameters)) $form .= ' ' . $parameters;

      $form .= '>' . "\r\n";
      $this->form++;

      return $form;
   }

   /**
    * Output form close
    * (decrement count)
    * @return string
    */
   function draw_form_close() {
      $this->form--;
      return '</form>' . "\r\n";
   }

   /**
    * Output a link (a href)
    * @param string $address
    * @param string/array $parameters = ''
    * @param string $contents = ''
    * @param string $target = ''
    * @return string
    */
   function draw_link($address, $parameters = '', $contents = '', $target = '') {
      if( $this->is_null($address) ) return false;

      $text_param = self::__unroll_params( self::__prepare_params($parameters) );

      if( $this->is_null($contents) ) $contents = $address;

      if( $this->not_null($target) ) $target = " taget=\"$target\"";

      return '<a href="' . $address . '" ' . $text_param . $target . '>' . $contents . '</a>';
   }

   /**
    * Output a form input field
    * @param string $name
    * @param string $value
    * @param string $param$parameterseters
    * @param string $type = 'text'pe
    * @param bool $reinsert_value = true
    * @return string
    *
    */
   //TODO think about default value and reinsert value
   function draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {

      $field = '<input type="' . $this->output_string($type) . '" name="' . $this->output_string($name) . '" id="' . $this->output_string($name) . '"';

      if ( ($reinsert_value == true) &&
      ( isset($this->REQUEST[$name]) && is_string($this->REQUEST[$name]) ) ) {
         $value = $this->REQUEST[$name];
      }

      if ($this->not_null($value)) {
         $field .= ' value="' . $this->output_string($value) . '"';
      }

      $field .= self::__unroll_params( self::__prepare_params($parameters) );

      $field .= '>' . "\r\n";

      return $field;
   }

   /**
    * Return button
    * @param string $text - name and value of button
    * @param string $parameters = ''
    * @return string
    */
   function draw_button($text, $parameters = '') {
      return self::draw_input_field($text, $text, $parameters, $type = 'button', false);
   }

   /**
    * Return submit button
    * @param string $text - name and value of button
    * @param string $parameters = ''
    */
   function draw_submit($text, $parameters = '') {
      return self::draw_input_field($text, $text, $parameters = '', $type = 'submit', false);
   }

   /**
    * Return password field
    * @param string $name
    * @param string $parameters = ''
    * @return string
    */
   function draw_password_field($name, $parameters = '') {
      return self::draw_input_field($name, '', $parameters, 'password', false);
   }

   /**
    * Output a selection field
    * @param string $name
    * @param string $type
    * @param string $value = ''
    * @param binary $checked = ''
    * @param string $parameters = ''
    * @return string
    */
   function draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {

      $selection = '<input type="' . $this->output_string($type) . '" name="' . $this->output_string($name) . '" id="' . $this->output_string($name) . '"';

      if ($this->not_null($value)) $selection .= ' value="' . $this->output_string($value) . '"';

      if ( ($checked == true) ||
      (isset($this->REQUEST[$name]) && is_string($this->REQUEST[$name]) &&
      ($this->REQUEST[$name] == 'on') || ($this->REQUEST[$name] == $value) ) ) {
         $selection .= ' CHECKED="CHECKED"';
      }

      if ($this->not_null($parameters)) $selection .= ' ' . $parameters;

      $selection .= '>';

      return $selection;
   }

   /**
    * Output a single checkbox field
    * @param string $name
    * @param string $value
    * @param binary $checked
    * @param string $parameters
    * @return string
    */
   function draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
      return $this->draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
   }

   /**
    * Output a single radio field
    * @param string $name
    * @param string $value
    * @param binary $checked
    * @param string $parameters
    * @return string
    */
   function draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
      return $this->draw_selection_field($name, 'radio', $value, $checked, $parameters);
   }

   /**
    * Output a form textarea field
    * @param string $name
    * @param string $wrap SOFT/HARD/OFF
    * @param int $width = 20
    * @param int $height = 5
    * @param string $text = ''
    * @param string $parameters = ''
    * @param string $reinsert_value = false
    * @return string
    */
   function draw_textarea_field($name, $wrap, $width = 20, $height = 5, $text = '', $parameters = '', $reinsert_value = true) {

      $field = '<textarea name="' . $this->output_string($name) . '" id="' . $this->output_string($name) .
      '" wrap="' . $this->output_string($wrap) . '" cols="' . $this->output_string($width) .
      '" rows="' . $this->output_string($height) . '"' . self::__unroll_params($parameters) . '>';

      if ( ($reinsert_value == true) &&
      ( isset($this->REQUEST[$name]) && is_string($this->REQUEST[$name])) ) {
         $field .= $this->output_string( $this->REQUEST[$name] );
      } elseif ($this->not_null($text)) {
         $field .= $this->output_string($text);
      }

      $field .= '</textarea>';

      return $field;
   }


   /**
    * Output a form hidden field
    * @param string $name
    * @param string $value = ''
    * @param string $parameters = ''
    * @return string
    */
   function draw_hidden_field($name, $value = '', $parameters = '') {

      $field = '<input type="hidden" name="' . $this->output_string($name) . '" id="' . $this->output_string($name) . '"';

      if ($this->not_null($value)) {
         $field .= ' value="' . $this->output_string($value) . '"';
      } elseif ( isset($this->REQUEST[$name]) && is_string($this->REQUEST[$name]) ) {
         $field .= ' value="' . $this->output_string($this->REQUEST[$name]) . '"';
      }

      if ($this->not_null($parameters)) $field .= ' ' . $parameters;

      $field .= '>';

      return $field;
   }

   /**
    * Output a form pull down menu
    * @param string $name
    * @param string $value
    * @param string $default = ''
    * @param string $parameters = array()
    * @param int $size = 10
    * @return string
    */
   function draw_select_menu($name, $values, $default = '', $parameters = array(), $size = 10) {

      $param_out['multiple'] = 'multiple';
      $param_out['size'] = (int)$size;

      if( !strstr($name, '[]') ) $name .= '[]';

      return $this->draw_pull_down_menu($name, $values, $default, $parameters = $param_out);

   }

   /**
    * Output a form pull down menu
    * @param unknown_type $name
    * @param unknown_type $values
    * @param unknown_type $default
    * @param unknown_type $parameters
    * @return string
    */
   function draw_pull_down_menu($name, $values, $default = array(), $parameters = array()) {

      $field = '<select name="' . $this->output_string($name) . '" id="' . $this->output_string($name) . '"' .
      self::__unroll_params($parameters) . '>' . NL;

      if( !is_array($default) ) {
         if(empty($default)) {
            $default = array();
         } else {
            $default = array($default);
         }
      }

      for ($i=0, $n=sizeof($values); $i<$n; $i++) {
         $field .= '<option value="' . $this->output_string($values[$i]['id']) . '"';
         if ( in_array($values[$i]['id'], $default) ) {
            $field .= ' selected="selected"';
         }

         $field .= '>' . $this->output_string($values[$i]['text']) . '</option>' . NL;
      }
      $field .= '</select>' . NL;

      return $field;
   }


}
?>
