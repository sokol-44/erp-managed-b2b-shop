<?php
/**
 * Class ArrayToXML
 *
 * Copyright Michał Sokołowski 2010
 *
 * Provides bidirectional conversion between multi-dimensional PHP arrays and XML documents.
 * It utilizes SimpleXMLElement for generation and a stream-based XMLReader parser via the internal
 * XMLToArray helper class for parsing.
 *
 * @author  Michał Sokołowski
 * @license AGPL 3.0
 *
 * @todo Refactor static methods into a concrete dependency-injected service class.
 * @todo Replace deprecated `zend.ze1_compatibility_mode` ini checks.
 * @todo Replace procedural/missing helper functions like `get_best_tmp_dir()` and `add_to_fp()` with standard PSR-compliant alternatives or loggers.
 * @todo Extract defined classes to separate files.
 * @todo Remove unused code.
 */

if (!defined('_I_INIT')) die();

class ArrayToXML
{
   /**
    * @var bool|XMLReader Internal XMLReader instance placeholder (unused within this scope).
    */
   private static $t_XMLReader = false;

   /**
    * Converts a multi-dimensional associative array into an XML string representation recursively.
    *
    * Handles nested arrays, object conversions via properties, and enforces XML node naming constraints.
    * It also checks and temporarily disables legacy Zend engine compatibility flags if active.
    *
    * @param array $data_in The multi-dimensional dataset to convert.
    * @param string $rootNodeName The tag name for the document root element. Defaults to 'DocumentElement'.
    * @param SimpleXMLElement|null $xml Internal recursive reference targeting the current parent node element.
    * @param array|null $info Optional supplementary metadata array to append as an 'Info' node element block.
    * @return string Returns the generated compliant XML structure formatted as a string expression.
    * @throws RuntimeException If SimpleXML fails to load or parse the basic XML initialization structure.
    *
    * @todo Refactor runtime `ini_set` configuration manipulation to system bootstrap or separate environment layer.
    * @todo Switch `htmlentities` or explicit UTF-8 normalization back on securely using `htmlspecialchars` to avoid invalid XML node body text payloads.
    * @todo Implement standard strict type declarations (`string`, `array`) on signature input arguments.
    */
   public static function toXml($data_in, $rootNodeName = 'DocumentElement', $xml=null, $info = null)
   {
      // turn off compatibility mode as simple xml throws a wobbly if you don't.
      if (ini_get('zend.ze1_compatibility_mode') == 1)
      {
         ini_set ('zend.ze1_compatibility_mode', 0);
      }

      if ($xml == null)
      {
//          if( function_exists('mb_get_info') && defined('DEFAULT_XML_OUTPUT_ENCODING')
//                && DEFAULT_XML_OUTPUT_ENCODING != 'UTF=8' ) {
//             $enc=DEFAULT_XML_OUTPUT_ENCODING ;
//          } else {
            $enc='UTF-8';
//          }
         $xml = simplexml_load_string('<?xml version="1.0" encoding="'.$enc.'"?><'.$rootNodeName.'/>');
         if( is_array($info) ) {
            $node = $xml->addChild('Info');
            ArrayToXML::toXml($info, 'Info', $node);
         }
      }

      // loop through the data passed in.
      foreach($data_in as $key => $value)
      {
         $key_a = explode('_', $key);
         if( is_array($key_a) && sizeof($key_a)==2 && $key_a[0] == 'value') {
            $xml_key = 'value';
         } else {
            $xml_key = $key;
         }
         // if there is another array found recursively call this function
         if (is_array($value))
         {
            $node = $xml->addChild($xml_key);
            // recursive call.
            ArrayToXML::toXml($value, $rootNodeName, $node);
         } elseif ( is_object($value) ) {
            $node = $xml->addChild($xml_key);
            ArrayToXML::toXml($value, $rootNodeName, $node);
         } else {
            // add single node.
            //$value = htmlentities($value);
//             if( function_exists('mb_get_info') && defined('DEFAULT_XML_OUTPUT_ENCODING')
//                && DEFAULT_XML_OUTPUT_ENCODING != 'UTF=8' ) {
//                $value = mb_convert_encoding($value, DEFAULT_XML_OUTPUT_ENCODING, 'UTF-8');
//             }
            $xml->addChild($xml_key,$value);
         }

      }
      // pass back as string. or simple xml object if you want!
      return $xml->asXML();
   }


   /**
    * Deserializes an XML document text string into a native PHP nested structured array mapping layout.
    *
    * Writes content safely to a temporary disk stream path location prior to pulling elements.
    *
    * @param string $string Raw source input string containing standard compliant target XML markup.
    * @param string $MainNodeName Structural context entry header node sequence pointer. Defaults to 'DocumentElement'.
    * @param string $ArrayNodeName Child record marker target name identifying repetitive item sequences. Defaults to 'value'.
    * @return array Multi-dimensional array collection breakdown representing payload node elements.
    * @throws RuntimeException If file I/O operations or temporary system context streams fail to open or close safely.
    *
    * @todo Extract local filesystem storage management workflow out to an explicit adapter dependency layer.
    */
   public static function Xmlto($string, $MainNodeName = 'DocumentElement', $ArrayNodeName = 'value') {
      $tmpfname = tempnam(get_best_tmp_dir(), 'b2b_xml_');
      $fd = fopen($tmpfname, 'w');
      fwrite($fd, $string); fflush($fd); fclose($fd);
      //add_to_fp(" $tmpfname ");
      $x2a = new XMLToArray($tmpfname, $MainNodeName, $ArrayNodeName);
      $res = $x2a->get_all_product_array();
      unlink($tmpfname);
      return $res;
   }
}

/**
 * Class XMLToArray
 *
 * Stream-based XML parsing engine utilizing PHP's XMLReader extensions.
 * Optimally designed to parse heavy payloads using a minimal framework memory footprint.
 *
 * @todo Add proper access modifiers (public/protected/private) to all internal class methods.
 * @todo Encapsulate class variables properly, declaring public variables as protected/private with accessors.
 * @todo Modernize property initializations using contemporary PHP constructor promotion properties.
 */
class XMLToArray {
   /**
    * @var XMLReader Engine processing instance tracking context stream positioning.
    */
   private $XMLReader;

   /**
    * @var string Targeted fully-qualified operating system local directory filepath destination context.
    */
   private $xml_file;

   /**
    * @var bool State condition monitoring if parser cursor reached completion boundary marker targets.
    */
   private $xml_end;

   /**
    * @var string XML structure document envelope outer parent root identification node tag name.
    */
   private $MainNodeName;

   /**
    * @var string Internal data element identity tracker denoting single items context groups.
    */
   private $ArrayNodeName;

   /**
    * @var array Array status logs capturing processing pipeline metrics or unexpected parse errors.
    */
   public $status = array();

   /**
    * @var array Output mapping dictionary collection matrix resulting from successful file extraction workflows.
    */
   public $values = array();

   /**
    * XMLToArray constructor.
    *
    * Initializes parsing targets, configures required properties, and invokes the stream reader validation cycle.
    *
    * @param string $fn Filepath location targeting accessible physical payload properties.
    * @param string $MainNodeName Enclosing root node element tracker.
    * @param string $ArrayNodeName Recurrent record entry label designation.
    *
    * @todo Eliminate raw echo statements and dead var_dump outputs from constructor runtime execution hooks.
    */
   function __construct($fn, $MainNodeName, $ArrayNodeName) {
      $this->MainNodeName = $MainNodeName;
      $this->ArrayNodeName = $ArrayNodeName;
      $this->xml_end = false;
      $this->xml_file = $fn;
      $this->init_products_data();
      // 		echo 'a'; echo var_dump($this->xml_end);
   }

   /**
    * Initializes structural properties and validates document compliance prior to triggering array translation.
    *
    * Sets up internal iteration markers and advances stream parameters past initial layout structures.
    *
    * @param int $skip Numeric index targeting total number of base data node items to fast-forward past.
    * @return array Status context tracking error description metrics or positive data confirmation codes.
    * @throws ErrorException When stream readers are forced to interact with unreadable files.
    *
    * @todo Replace arbitrary nested procedural trace trackers like `add_to_fp()` with proper PSR-3 Logger interfaces.
    * @todo Declare scope context for variables such as `$products_start` that are used inside loop constraints without standard definitions.
    */
   function init_products_data($skip = 0) {
      add_to_fp(' init_products_data ' . $this->xml_file);
      $fd = fopen($this->xml_file, 'r');
      $fc = stream_get_contents( $fd );
      $this->XMLReader = new XMLReader();
      if( $this->XMLReader->open($this->xml_file) ) {
         $node_count = false;
         $this->xml_end = false;
         $node_count = 0;
         $this->status = array('STATUS' => false, 'TYPE' => '0', 'DESCRIPTION' => 'NO_XML_VALID');
      } else {
         $this->status = array('STATUS' => false, 'TYPE' => '0', 'DESCRIPTION' => 'NO_XML_OPEN');
      }

      while ( ($res_read = $this->XMLReader->read() ) ) {
         //skip till

         if(	$this->XMLReader->nodeType==XMLReader::SIGNIFICANT_WHITESPACE ) continue;


         if( $products_start ) {
            if(	$this->XMLReader->nodeType==XMLReader::ELEMENT &&
                  $this->XMLReader->name==$this->ArrayNodeName) {
               $this->status = array('STATUS' => true, 'TYPE' => '1', 'DESCRIPTION' => 'XML_BEGIN');
               //					echo 'QQQ';
               break;
            }
         } else {
            if(	$this->XMLReader->nodeType==XMLReader::ELEMENT &&
                  $this->XMLReader->name==$this->MainNodeName) {
               $products_start = true;
               continue;
            }
         }
         if( $node_count > 100) {
            $this->status = array('STATUS' => false, 'TYPE' => '0', 'DESCRIPTION' => 'NO_XML_VALID_FILETYPE');
            $this->xml_end = true;
            break;
         }
         $node_count++;
      }

      //if everything allright
      if( $res_read && $this->status['STATUS'] ) {
         //		$skip = 1;
         if( $skip > 0 ) $this->status = $this->xmlrewind($skip);

         if( $this->status['TYPE'] == 0 ) $this->xml_end = true;
      } else {
         $this->xml_end = true;
      }


      return $this->status;
   }

   /**
    * Fast-forwards the active XMLReader parser pointer instance past a specified count of array-node items.
    *
    * @param int $skip Numeric item counter value outlining target position index parameters.
    * @return array Execution runtime state feedback metadata properties mapping.
    *
    * @todo Standardize naming typos in strings ("XML_REVIND" should be "XML_REWIND").
    * @todo Refactor hardcoded scalar comparisons targeting string literals such as 'product'.
    */
   function xmlrewind($skip) {
      //		echo "REWIND$skip\r\n";
      if($this->XMLReader->name == $this->ArrayNodeName) {
         for($n=0;$n<$skip;$n++) $this->XMLReader->next($this->ArrayNodeName);
      }
      if($this->XMLReader->name != 'product' ) return array('STATUS' => true, 'TYPE' => '0', 'DESCRIPTION' => 'XML_END');
      return array('STATUS' => true, 'TYPE' => $skip, 'DESCRIPTION' => 'XML_REVIND');
   }

   /**
    * Isolates and returns the immediate single array segment block context matching array node indicators.
    *
    * @return array|string|false Associated key mapping schema array representation, text content, or false.
    */
   function get_next_product_array() {
      $res_xml = $this->xml2assoc($this->ArrayNodeName);

      return $res_xml;
   }

   /**
    * Iterates completely through remaining elements to generate an encompassing multi-dimensional associative output array.
    *
    * @return array Context payload housing compilation tracking status codes along with value arrays.
    *
    * @todo Refactor array construction keys (`$this->ArrayNodeName . '_' . $node_idx`) to avoid arbitrary key indexing naming convention fragmentation.
    */
   function get_all_product_array() {
      $res_xml = array();
      $node_idx = 0 ;
      if( $this->check_products_end() ) {
         while( $this->check_products_end() ) {
            $res_xml[$this->ArrayNodeName . '_' . $node_idx] = $this->xml2assoc($this->ArrayNodeName);
            if( !$this->check_products_end() ) {
               $this->status = array('STATUS' => false, 'TYPE' => '1', 'DESCRIPTION' => 'XML_END');
            }
            $node_idx++;
         }
      }
      $this->values = $res_xml;
      $this->XMLReader->close();
      return array('status' => $this->status, 'values' => $this->values);
   }

   /**
    * Verifies if parsing streams remain open or if processing execution flags have indicated completion limits.
    *
    * @return bool Returns true if data loops remain processing, false otherwise.
    */
   function check_products_end() {
      return !$this->xml_end;
   }

   /**
    * Recursively maps active stream reader XML structural hierarchies into an associative multi-dimensional target array.
    *
    * Iterates node types, parsing inline element markup attributes, text nodes, and closing tags seamlessly.
    *
    * @param string $el_name The structural target label configuration name of the element node to translate.
    * @param int $level Nesting level indicator depth tracker parameter context. Defaults to 0.
    * @return array|string|false Key structural array map representation, node inner text contents, or false if stream ends.
    *
    * @todo Erase error-suppression operator symbols (`@$this->XMLReader->read()`) and use explicit try-catch exception models.
    * @todo Fix implicit reference pass operations (`$el =& $assoc[...]`) targeting dynamic array lookups which present warnings in PHP 8+.
    * @todo Remove dead commented tracking logs, debug array definitions (`$deb`), and trailing test print instructions.
    */
   function xml2assoc($el_name, $level = 0) {
      $assoc = array();
      $deb = array();

      // echo '$' . $this->XMLReader->name . "$\r\n";
      while( ($res_read = @$this->XMLReader->read()) ){
         switch ($this->XMLReader->nodeType) {
            case XMLReader::END_ELEMENT:
               if( $this->XMLReader->name == $el_name ) {
                  if( sizeof($assoc) == 0 ) $assoc = '';
                  break 2;
               }
               break;
            case XMLReader::ELEMENT:

               if($this->XMLReader->hasAttributes){
                  $el =& $assoc[$this->XMLReader->name][count($assoc[$this->XMLReader->name]) - 1];
                  while($this->XMLReader->moveToNextAttribute()) {
                     $el['ATTRIBUTES'][$this->XMLReader->name] = $this->XMLReader->value;
                  }
               } else {
                  if( $this->XMLReader->isEmptyElement ) {
                     $assoc[$this->XMLReader->name] =  '';
                  } else {
                     if( $this->XMLReader->name == $this->ArrayNodeName && $level>0) {
                        $assoc[] = $this->xml2assoc($this->XMLReader->name, $level+1);
                        $deb[] = '___ l:'.$level.' t:'  . $this->XMLReader->nodeType .' n:'.$this->XMLReader->name . ' #v:' . $this->XMLReader->value . "#";
                     } else {
                        $assoc[$this->XMLReader->name] = $this->xml2assoc($this->XMLReader->name, $level+1);
                        $deb[] = '    l:'.$level.' t:'  . $this->XMLReader->nodeType .' n:'.$this->XMLReader->name . ' #v:' . $this->XMLReader->value . "#";
                     }
                  }

               }
               break;
            case XMLReader::TEXT:
            case XMLReader::CDATA: if($this->XMLReader->value!='') $assoc = $this->XMLReader->value;
         }
         //add_to_fp('$xml2assoc'.$level.':'  . $this->XMLReader->nodeType .'n'.$this->XMLReader->name . ' #' . $this->XMLReader->value . "#");;
      }

//       if( sizeof($assoc) == 0 ) $assoc = $this->XMLReader->nodeType;

      if( !$res_read ) {
         // echo '$#' . $this->XMLReader->name . '$' . $this->XMLReader->value . "$\r\n";
         $this->xml_end = true;
         return false;
      }

      if($el_name == $this->ArrayNodeName && $level==0) {
         while($this->XMLReader->read()){
            if($this->XMLReader->nodeType == XMLReader::ELEMENT && $this->XMLReader->name == $this->ArrayNodeName)
               break;
            if($this->XMLReader->nodeType == XMLReader::END_ELEMENT && $this->XMLReader->name == $this->MainNodeName) {
               $this->xml_end = true;
               //					echo "in#" . ($this->check_products_end()?'cpe-TRUE':'cpe-FALSE') . '#' . ($this->xml_end?'xml_end-TRUE':'xml_end-FALSE') . "#\r\n";
               //					echo 'EEE';
               break;
            }
         }
      }

      //if( sizeof($deb) > 0 ) add_to_fp(print_r($deb, true));
      return $assoc;
   }

}
