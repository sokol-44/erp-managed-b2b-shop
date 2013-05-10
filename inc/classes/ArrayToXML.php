<?php
class ArrayToXML
{
   private static $t_XMLReader = false;
   /**
    * The main function for converting to an XML document.
    * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
    *
    * @param array $data_in
    * @param string $rootNodeName - what you want the root node to be - defaultsto data.
    * @param SimpleXMLElement $xml - should only be used recursively
    * @param info $xml - should only be used recursively
    * @return string XML
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
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
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
         // if there is another array found recrusively call this function
         if (is_array($value))
         {
            $node = $xml->addChild($xml_key);
            // recrusive call.
            ArrayToXML::toXml($value, $rootNodeName, $node);
         } elseif ( is_object($value) ) {
            $node = $xml->addChild($xml_key);
            ArrayToXML::toXml($value, $rootNodeName, $node);
         } else {
            // add single node.
            //$value = htmlentities($value);
            $xml->addChild($xml_key,$value);
         }
         	
      }
      // pass back as string. or simple xml object if you want!
      return $xml->asXML();
   }


   public static function Xmlto($string, $MainNodeName = 'DocumentElement', $ArrayNodeName = 'value') {
      $tmpfname = tempnam(sys_get_temp_dir(), 'b2b_xml_');
      file_put_contents($tmpfname, $string);
      $x2a = new XMLToArray($tmpfname, $MainNodeName, $ArrayNodeName);
      $res = $x2a->get_all_product_array();
      unlink($tmpfname);
      return $res;
   }
}

class XMLToArray {
   private $XMLReader;
   private $xml_file;
   private $xml_end;
   private $MainNodeName;
   private $ArrayNodeName;
   public $status = array();
   public $values = array();

   function __construct($fn, $MainNodeName, $ArrayNodeName) {
      $this->MainNodeName = $MainNodeName;
      $this->ArrayNodeName = $ArrayNodeName;
      $this->xml_end = false;
      $this->xml_file = $fn;
      print_r($this->init_products_data());
      // 		echo 'a'; echo var_dump($this->xml_end);
   }

   function init_products_data($skip = 0) {

      $this->XMLReader = new XMLReader();
      if( $this->XMLReader->open($this->xml_file) ) {
         $products_start = false;
         $this->xml_end = false;
         $node_count = 0;
         $this->status = array('STATUS' => false, 'TYPE' => '0', 'DESCRIPTION' => 'NO_XML_VALID');
      } else {
         $this->status = array('STATUS' => false, 'TYPE' => '0', 'DESCRIPTION' => 'NO_XML_OPEN');
      }

      while ( ($res_read = $this->XMLReader->read() ) ) {
         //skip till
         echo '$' . $this->XMLReader->name . ' #' . $this->XMLReader->value . "#\r\n";
         	
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

   function xmlrewind($skip) {
      //		echo "REWIND$skip\r\n";
      if($this->XMLReader->name == $this->ArrayNodeName) {
         for($n=0;$n<$skip;$n++) $this->XMLReader->next($this->ArrayNodeName);
      }
      if($this->XMLReader->name != 'product' ) return array('STATUS' => true, 'TYPE' => '0', 'DESCRIPTION' => 'XML_END');
      return array('STATUS' => true, 'TYPE' => $skip, 'DESCRIPTION' => 'XML_REVIND');
   }

   function get_next_product_array() {
      $res_xml = $this->xml2assoc($this->ArrayNodeName);

      return $res_xml;
   }

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

   function check_products_end() {
      return !$this->xml_end;
   }

   function xml2assoc($el_name) {
      $assoc = array();

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
                     $assoc[$this->XMLReader->name] = $this->xml2assoc($this->XMLReader->name);
                  }
                   
               }
               break;
            case XMLReader::TEXT:
            case XMLReader::CDATA: if($this->XMLReader->value!='') $assoc = $this->XMLReader->value;
         }
      }
      
//       if( sizeof($assoc) == 0 ) $assoc = $this->XMLReader->nodeType;

      if( !$res_read ) {
         // echo '$#' . $this->XMLReader->name . '$' . $this->XMLReader->value . "$\r\n";
         $this->xml_end = true;
         return false;
      }

      if($el_name == $this->ArrayNodeName) {
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

      return $assoc;
   }
    
}
?>