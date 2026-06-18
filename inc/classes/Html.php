<?php
/**
 * HTML.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski
 * @license AGPL 3.0
 */

if( !defined('_I_INIT') ) die();

/**
 * Class HTML
 *
 * Provides view-layer layout management, DOM component factories, dynamic image rendering,
 * and semantic matrix rendering parameters structures for front-end template scopes.
 *
 * @designPattern Factory
 *
 * @todo Declare visibility modifiers (public/protected/private) explicitly over all structural method scopes.
 * @todo Apply standard scalar types, class mappings, and explicit union/nullable return types.
 * @todo Abstract the static state management and globally scoped references (e.g. `Framework::g_global()`).
 * @todo Decouple custom view-layer data cache assignments (`$this->{$name}`) onto formal entity collection objects.
 */
class HTML {


   /**
    * Builds file-system destinations and complete web asset paths matching absolute or local sources.
    *
    * @param string $src Relative file path or absolute image target directory string definition.
    * @param bool $admin Optional context configuration switch modifying routing contexts. Defaults to false.
    * @return array Enumerated vector index containing the computed system path and destination URL layout mapping.
    *
    * @todo Unused argument parameters (`$admin`) should be removed or integrated into localized routing rules.
    */
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

   /**
    * Analyzes local system file definitions to draw dimensional attributes.
    *
    * @param string $src System pathway specification string pointing to an image file.
    * @return array|bool Dimensional metrics dictionary listing width and height on success, or false on missing configurations.
    */
   private function __getimagesize($src) {
      if( is_file( $src ) && is_readable( $src )  ) {
         $image_size = getimagesize($src);
         if( $image_size ) {
            return array('width' => $image_size[0], 'height' => $image_size[1]);
         }
      }
      return false;
   }



   /**
    * Outputs an image tag containing parameters gathered from binary asset records indexes.
    *
    * @param int|string $id Target unique database image entry tracking key or identity string indicator.
    * @param string $type Target configuration standard size criteria ('SMALL', 'NORMAL', 'ORIGINAL'). Defaults to ''.
    * @param string $alt Description translation substitution string used for accessibility purposes. Defaults to ''.
    * @param array|string $parameters Supplementary presentation design definitions properties lists. Defaults to ''.
    * @return string Compiled HTML img marker layout specification string.
    *
    * @todo Rectify call dependencies pointing to non-existent context endpoints (`Data::get_subpicture_data`).
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

   /**
    * Resolves the direct URL link pathway targeting a localized static web image asset.
    *
    * @param string $src Relative target address pointer character location string.
    * @return string Resolved public destination URL asset framework string representation.
    */
   function static_image_src($src) {
           list(, $www_src) = self::__create_image_paths($src, true);
           return $www_src;
   }

   /**
    * Wraps a static graphic file resource inside a standardized HTML image markup tag.
    *
    * @param string $src File source locator tracking asset address location string definition.
    * @param string $alt Context text representation applied to alternative output segments. Defaults to ''.
    * @param mixed $parameters Inline styles or layout parameter settings overrides flags list variables. Defaults to ''.
    * @param int|string $width Intended destination frame width boundary value context alignment parameter. Defaults to ''.
    * @param int|string $height Intended destination frame height boundary value context alignment parameter. Defaults to ''.
    * @return string|bool Assembled relative tag formatting indicator stream, or false on verification failure states.
    *
    * @todo Fix logical defects caused by invoking non-existent formatting methods directly inside class scope (`$this->output_string`).
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

   /**
    * Returns the query link address generated for dynamic text button processing instances.
    *
    * @param string $text Label description text targeted for runtime graphic transformation. Defaults to ''.
    * @param mixed $template Optional layout rule parameters adjusting visual styles overlays. Defaults to ''.
    * @return string Path locator string capturing target dynamic parameters definitions values.
    *
    * @todo Unused visual argument settings parameters (`$template`) should be cleanly eliminated from interface signatures.
    */
   function dynamic_image_src($text = '', $template = '') {

      if( $text == '' ) $text = '       ';
      $txt_param = '?' . IMAGE_BUTTON_SCRIPT_TEXT_PARAM . '=' . rawurlencode($text);

      return DIR_HTTP_ROOT_CATALOG . IMAGE_BUTTON_SCRIPT . $txt_param;
   }

   /**
    * Generates a fully formatted image element wrapping dynamic script generator outcomes.
    *
    * @param string $text Alphanumeric character sequence applied to the visual layout. Defaults to ''.
    * @param string $template Layout parameter tracking style choice markers variables. Defaults to ''.
    * @return string Assembled dynamic layout code text stream specification.
    */
   function dynamic_image($text = '', $template = '') {

      $src = $this->dynamic_image_src($text, $template);

      $image = '<img src="' . $src . '" border="0" alt="' . $this->output_string($text) .
      '" title="' . $this->output_string($text) . '">';

      return $image;
   }

   /**
    * Generates standard button markup representations leveraging specialized text assets models.
    *
    * @param string $text Alphanumeric content label notation format. Defaults to ''.
    * @param string $name Selector naming key attribute mapping target element. Defaults to ''.
    * @param string $template Visual schema configuration style options settings context. Defaults to ''.
    * @return string Output component presentation data markup template block string.
    *
    * @todo Eliminate dead properties components arguments metrics from signature architectures (`$name`).
    */
   function dynamic_image_button($text = '', $name='', $template = '') {
      return $this->dynamic_image($text, $template);
   }

   /**
    * Factory utility returning input dynamic form submission indicators image structures tags.
    *
    * @param string $text Verification indicator text tag tracking asset details profiles metrics. Defaults to ''.
    * @param string $name Form element property name attribute identifier context mapping value. Defaults to ''.
    * @param string $parameters Supplementary parameters array profiles settings maps overrides. Defaults to ''.
    * @return string Structural HTML markup template layout target block.
    *
    * @todo Clean up and fix code dependencies calling undefined external legacy variables scopes (`global $language;`).
    */
   function dynamic_image_submit($text = '', $name='', $parameters = '') {
      global $language;

      $src = $this->dynamic_image_src($text);

      return $this->static_image_submit($src, $text, 'name="' . $this->output_string($name) . '"');
   }

   /**
    * Supplies the standard relative image asset root path context matching simple file labels.
    *
    * @param string $image Target file naming structure character sequence value parameter.
    * @return string Calculated public site address destination location string format definition.
    */
   function static_image_path($image) {
      return DIR_WWW_IMG . $image;
   }

   /**
    * Builds an input graphic component used for form submission loops templates.
    *
    * @param string $image Calculated direct web URI pathway source tracking target files destinations.
    * @param string $alt Description properties validation marker metadata for layout accessibility rules. Defaults to ''.
    * @param string $parameters Inline attributes parameters changes strings collection values configurations. Defaults to ''.
    * @return string Structural view presentation string placeholder block formatting.
    */
   function static_image_submit($image, $alt = '', $parameters = '') {
      global $language;

      $image_submit = '<input type="image" src="' . $image . '" border="0" alt="' . $this->output_string($alt) . '"';

      if ($this->not_null($alt)) $image_submit .= ' title=" ' . $this->output_string($alt) . ' "';

      if ($this->not_null($parameters)) $image_submit .= ' ' . self::__unroll_params( self::__prepare_params($parameters) );;

      $image_submit .= '>';

      return $image_submit;
   }

   /**
    * Builds standard button structures matching specified interface localized translation directories.
    *
    * @param string $image Target button graphic asset description filename code string notation format.
    * @param string $alt Visual annotation tracking metric for screen reader layers definitions. Defaults to ''.
    * @param string $parameters Extra properties changes choices vector metadata listings frameworks. Defaults to ''.
    * @return string Assembled relative view presentation template asset block.
    */
   function static_image_button($image, $alt = '', $parameters = '') {
      global $language;

      return $this->static_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, $parameters, '', '');
   }

   /**
    * Outputs an inline block of JavaScript execution code forcing immediate portal relocations paths.
    *
    * @param string $page Target address link directory pathway configuration destination string.
    * @return string Client visual scripting execution directive string block layout.
    */
   function put_js_redirect($page) {
      return "<script>location.href='$page';</script>";
   }

   /**
    * Renders an isolated spacer row or black partition layout line segment across page sections.
    *
    * @param string $image Target separation image indicator filename standard reference rule. Defaults to 'pixel_black.gif'.
    * @param string $width Transverse layout boundary scaling horizontal measure metric parameter. Defaults to '100%'.
    * @param string $height Transverse layout boundary scaling vertical measure metric parameter. Defaults to '1'.
    * @return string Structural placeholder view element presentation block component.
    *
    * @todo Fix major logical bug where method references non-existent object endpoint mappings (`$this->image`).
    */
   function draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
      return $this->image(DIR_WS_IMAGES . $image, '', $width, $height);
   }

   /**
    * Base placeholder hook targeting master system grid allocations architectures setups.
    *
    * @return void
    */
   function init_table() {

   }

   /**
    * Instantiates custom structural storage caches inside local instance configurations tracking parameters variables.
    *
    * @param string $name Allocation layout data selector indicator text string constraint rule code. Defaults to ''.
    * @return void
    */
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

   /**
    * Registers an array segment placeholder tracking records inside localized container arrays maps buffers.
    *
    * @param string $name Identification target storage container selection key text. Defaults to ''.
    * @param string|array $params Inline attributes context specifications detailing rows definitions options. Defaults to ''.
    * @return void
    */
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

   /**
    * Maps item details blocks components onto the active cell row tracking indices locations profiles.
    *
    * @param string $name Master grid directory cache container selector code text string reference. Defaults to ''.
    * @param string $content Visual description block or text content wrapped inside table cells markers. Defaults to ''.
    * @param string $type Component selector defining standard layout types ('td', 'th'). Defaults to 'td'.
    * @param string|array $params Supplementary specification properties changes arrays logs details maps. Defaults to ''.
    * @return bool|void Returns false exclusively if the destination dataset container is uninitialized.
    */
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

   /**
    * Returns the array container mapping all generated layout elements values cached matching target labels.
    *
    * @param string $name Target buffer identity lookup code text selection option parameter settings. Defaults to ''.
    * @return array Collected segment dataset collection structural matrix elements rows lists.
    */
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

   /**
    * Transforms dictionary arrays lists properties configurations parameters into standard flat strings formats lines.
    *
    * @param array|string $params Context options specifications dictionary target processed for text extraction.
    * @return string Formatted attribute assignment sequence line containing safe parameter attributes declarations codes.
    *
    * @todo Fix logical bug where non-array string returns attempt to reference unassigned variables parameters structures (`$params_array`).
    */
   static function __unroll_params($params) {
      $param_str = '';
      if( Framework::not_null($params) ) {
         if( is_array($params) ) {
            $tr_parm = array();
            foreach( $params as $attr => $val ) {
                if( is_numeric($attr) ) $tr_parm[] = $val . '="' . $val . '" ';
                else $tr_parm[] = $attr . '="' . $val . '" ';
            }
            return ' ' . implode(' ', $tr_parm);
         } else {
            return ' ' . $params_array;
         }
      } else {
         return $param_str;
      }
   }

   /**
    * Translates raw text options specifications chains into clean dynamic parameters properties indices arrays.
    *
    * @param array|string $params_in Un-sorted target configurations dataset or character string parameters list data maps.
    * @return array Keyed properties definitions parameter updates options arrays lists.
    */
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
                  $params[$key] = $val;
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

   /**
    * Normalizes unstructured text lines tokens vectors into structured multi-selection indexes list options.
    *
    * @param array|string $selected_in Target tracking selected values dataset collection mapping indicator arrays.
    * @return array Linear elements lists representation parameters matching verified states logs rules.
    */
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
    * Iterates dynamic multi-layer grid array collections converting them to fully rendered table rows elements.
    *
    * @param array $rows_array Compiled structure container mapping multiple cells data definitions metrics rows.
    * @param bool $checkbox Structural interface rule controlling input option visibility flags. Defaults to true.
    * @return string View presentation layer text stream document block formatting.
    *
    * @todo Rectify cell validation notices caused by calling conditional arrays evaluations directly inside nested loops fields (`$row['boxparam']`).
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

   /**
    * Formulates standard secure opening container parameters blocks wrapping input data forms streams.
    *
    * @param string $name Identity property index naming token assigned to target element node.
    * @param string $action Endpoint target navigation address path processing parameters submissions.
    * @param string $method Transmission scheme guidelines format context definition identifier string rule ('post', 'get'). Defaults to 'post'.
    * @param string $parameters Extra properties choices lines configurations strings frameworks parameters. Defaults to ''.
    * @return string View markup template placeholder component framework block.
    */
   function draw_form($name, $action, $method = 'post', $parameters = '') {

      $form = '<form id="' . $this->output_string($name) . '" name="' . $this->output_string($name) . '" action="' . $action . '" method="' . $this->output_string($method) . '"';
      if ($this->not_null($parameters)) $form .= ' ' . $parameters;

      $form .= '>' . "\r\n";
      $this->form++;

      return $form;
   }

   /**
    * Formulates explicit closing elements terminations segments wrapping data entry form structures.
    *
    * @return string View layout closing element string notation format.
    */
   function draw_form_close() {
      $this->form--;
      return '</form>' . "\r\n";
   }

   /**
    * Returns an encapsulated mail to anchor element verified through regular validation filters rules.
    *
    * @param string $address Target digital transmission mailbox path string parameter configuration.
    * @param array|string $parameters Extra specification traits metrics logs details mappings. Defaults to ''.
    * @param string $contents Descriptive label text or content enclosed inside tag markers. Defaults to ''.
    * @param string $target Dynamic window behavior rules routing parameter specifications. Defaults to ''.
    * @return string|bool Presentation element structural template string text block, or false on mapping query delivery faults.
    */
   function draw_email_link($address, $parameters = '', $contents = '', $target = '') {
      if( !Framework::check_valid_email($address) ) return false;

      if( $this->is_null($contents) ) $contents = $address;

      return $this->draw_link('mailto:'.$address, $parameters, $contents, $target);
   }

   /**
    * Builds a traditional anchor hyperlink element wrapping custom properties changes definitions.
    *
    * @param string $address Destination locator route path tracking index criteria values configurations.
    * @param array|string $parameters Inline presentation parameters adjustments strings listings. Defaults to ''.
    * @param string $contents Informational text values displayed inside design layouts. Defaults to ''.
    * @param string $target Target configuration context framing rules choices. Defaults to ''.
    * @return string|bool HTML layout code block string on success, or false on execution evaluation errors.
    */
   function draw_link($address, $parameters = '', $contents = '', $target = '') {
      if( $this->is_null($address) ) return false;

      $text_param = self::__unroll_params( self::__prepare_params($parameters) );

      if( $this->is_null($contents) ) $contents = $address;

      if( $this->not_null($target) ) $target = " taget=\"$target\"";

      return '<a href="' . $address . '" ' . $text_param . $target . '>' . $contents . '</a>';
   }

   /**
    * Generates an input text or specific functional data entry component field node within layout grids.
    *
    * @param string $name Unique reference identifier index key applied to element name attributes.
    * @param string $value Fallback or default parameter details text payload content specification. Defaults to ''.
    * @param string $parameters Inline changes parameters string vector description matrices arrays checklist. Defaults to ''.
    * @param string $type Input formatting element variant type criteria standards rule token. Defaults to 'text'.
    * @param bool $reinsert_value Evaluation check enabling context retrieval from active request arrays blocks. Defaults to true.
    * @return string Structured presentation HTML marker element template stream.
    */
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
    * Renders a basic clickable option button element inside visualization layers.
    *
    * @param string $text Title description and value parameter assigned onto the item tag attributes.
    * @param string $parameters Supplementary formatting checklist parameters arrays logs properties. Defaults to ''.
    * @return string View layout element string component formatting.
    */
   function draw_button($text, $parameters = '') {
      return self::draw_input_field($text, $text, $parameters, $type = 'button', false);
   }

   /**
    * Renders an input form transaction action submission button placeholder component framework.
    *
    * @param string $text Reference identification key code mapping value parameters.
    * @param string $parameters Supplementary inline properties values variables array configurations options. Defaults to ''.
    * @param string|bool $value Custom label description override string, or false to default to tracking keys text. Defaults to false.
    * @return string View layout component data markup presentation string block.
    *
    * @todo Fix signature syntax properties bugs generated via tracking default fallback arguments assignments codes definitions (`$parameters = ''`).
    */
   function draw_submit($text, $parameters = '', $value = false) {
      if( !$value ) $value = $text;
      return self::draw_input_field($text, $value, $parameters = '', $type = 'submit', false);
   }

   /**
    * Renders an obscured masking data access parameters entry container field node within active forms templates.
    *
    * @param string $name Identification locator string mapping element parameters configurations tags.
    * @param string $parameters Supplementary specification definitions choices vectors variables lists. Defaults to ''.
    * @return string Presentation element markup placeholder block string format.
    */
   function draw_password_field($name, $parameters = '') {
      return self::draw_input_field($name, '', $parameters, 'password', false);
   }

   /**
    * Core factory returning specialized conditional selector fields elements nodes matching validation types tracking codes.
    *
    * @param string $name Unique reference identifier tracking key index code applied onto properties fields.
    * @param string $type Selection standard item layout specification code criteria context parameter ('checkbox', 'radio').
    * @param string $value Text payload descriptor parameter representing structural content value targets. Defaults to ''.
    * @param bool $checked Option switch checking active validation indicators rules parameters. Defaults to false.
    * @param string $parameters Extra properties changes arrays listings templates metadata guidelines. Defaults to ''.
    * @return string Layout markup representation code block component.
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
    * Renders a checkbox input switch field element inside visualization layout frameworks.
    *
    * @param string $name Identification parameter label token checked against context indexes tracking codes.
    * @param string $value Action definition code metrics tracking selection states definitions fields. Defaults to ''.
    * @param bool $checked Selection indicator tracking confirmation baseline parameter rules context. Defaults to false.
    * @param string $parameters Extra specifications modifications options checklists metrics logs. Defaults to ''.
    * @return string Structural HTML markup template layout target block description string.
    */
   function draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
      if( $value == '' ) $value = 'on';
      return $this->draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
   }

   /**
    * Renders a single mutually exclusive option selector circle node inside operational forms spaces.
    *
    * @param string $name Master group classification identifier string tracking shared fields choices.
    * @param string $value Action parameter content values definition code targeting system processing. Defaults to ''.
    * @param bool $checked Active selection status indication parameter switch rules verification. Defaults to false.
    * @param string $parameters Supplementary structural elements tweaks descriptors arrays vector lists. Defaults to ''.
    * @return string View component data markup placeholder block string format.
    */
   function draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
      return $this->draw_selection_field($name, 'radio', $value, $checked, $parameters);
   }

   /**
    * Renders an extended multi-line prose data description entry textbox element inside visualization grids.
    *
    * @param string $name Unique reference identifier keyword token mapping name configurations targets.
    * @param string $wrap Text justification constraint standard strategy codes parameters definitions ('SOFT', 'HARD', 'OFF').
    * @param int $width Grid boundary scaling horizontal columns boundary capacity indicator parameter. Defaults to 20.
    * @param int $height Grid boundary scaling vertical lines context boundary capacity indicator parameter. Defaults to 5.
    * @param string $text Baseline or default long block prose descriptions content text. Defaults to ''.
    * @param array|string $parameters Extra dynamic specifications criteria choices array properties logs checklist. Defaults to ''.
    * @param bool $reinsert_value Option switch pulling user inputs cache states from active global data arrays buffers. Defaults to true.
    * @return string Presentation element structural template design placeholder string text.
    */
   function draw_textarea_field($name, $wrap, $width = 20, $height = 5, $text = '', $parameters = '', $reinsert_value = true) {

      $parameters = self::__prepare_params($parameters);

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
    * Emits standard hidden parameter values tracking tags storing metadata without rendering visual boxes.
    *
    * @param string $name Target identification criteria keyword token matching data model mappings options.
    * @param string $value Content state payload reference text payload details code parameter settings. Defaults to ''.
    * @param string $parameters Extra dynamic inline adjustments configurations lists variables frameworks. Defaults to ''.
    * @return string Component data markup placeholder block layout string structure.
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
    * Standard combo builder factory assembling complex multiple choice list select options layouts parameters.
    *
    * @param string $name Group tracking destination identity tag variable name criteria string rules.
    * @param array $values Multidimensional array data rows mapping option fields descriptors targets lists.
    * @param string $default Target identification reference option indicating pre-selected items configurations. Defaults to ''.
    * @param array $parameters Supplementary specifications elements modifications indicators array mappings. Defaults to array().
    * @param int $size Vertical frame capacity boundary metrics depth scale limit value settings constraints. Defaults to 10.
    * @return string View representation layer text stream document layout block formatting.
    */
   function draw_select_menu($name, $values, $default = '', $parameters = array(), $size = 10) {

      $param_out['multiple'] = 'multiple';
      $param_out['size'] = (int)$size;
      $parameters = array_merge($parameters, $param_out);

      if( !strstr($name, '[]') ) $name .= '[]';

      return $this->draw_pull_down_menu($name, $values, $default, $parameters);
   }

   /**
    * Assembles a multi-option drop down choice selection context container component field node within layout grids.
    *
    * @param string $name Target identification dynamic string token indicator mapping data structures.
    * @param array $values Sequential collection matrix detailing unique identifier value parameters rows arrays.
    * @param array|string $default Pre-assigned item parameters selections settings layout checklists keys descriptors. Defaults to array().
    * @param array $parameters Extra parameters modifications adjustments choices mappings properties hashes list. Defaults to array().
    * @return string Structural HTML markup template layout target code output component stream.
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

      foreach( $values as $value ) {
         $field .= '<option value="' . $this->output_string($value['id']) . '"';
         if ( in_array($value['id'], $default) ) {
            $field .= ' selected="selected"';
         }

         $field .= '>' . $this->output_string($value['text']) . '</option>' . NL;
      }
      $field .= '</select>' . NL;

      return $field;
   }

}
