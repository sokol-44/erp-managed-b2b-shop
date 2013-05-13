<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();

/**
 * Data class goint to provide data and operation on them
 * Application will get and save any data thru it
 */
/**
 * @author ms
 *
 */
class Data_Article extends Data_Contact{
   
   static function get_article($key) {
      
      $query = 'select a.id_article, a.date_created, a.date_modified, a.title, a.content
      from ' . TBL_GLOBAL_ARTICLE . ' a
      where a.id_article = ' . db_int($key) . '';
      $result = db_query( $query );
      return db_fetch_array($result);
   }
}

?>