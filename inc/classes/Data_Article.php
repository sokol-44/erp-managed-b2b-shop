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
 * Class Data_Article
 *
 * Provides data operations specifically for articles, extending the contact data functionality.
 *
 * @todo Apply modern PHP namespaces (e.g., namespace App\Data;).
 * @todo Rename class to PascalCase to comply with PSR-1/PSR-12 naming conventions (e.g., ArticleRepository).
 * @todo Implement proper Dependency Injection (DI) for database persistence layers rather than relying on global procedural drivers.
 */
class Data_Article extends Data_Contact{

    /**
     * Data_Article constructor.
     *
     * Initializes the article data object and calls the parent constructor.
     *
     * @return void
     *
     * @todo Add explicit public visibility modifier.
     */
    function __construct() {
        //echo get_class();
        parent::__construct();

    }

   /**
    * Retrieve an article by its unique key.
    *
    * Fetches article details including ID, creation date, modification date, title, and content
    * from the database using the provided key.
    *
    * @param int|string $key The unique identifier of the article.
    * @return array<string, mixed>|false The article data as an associative array, or false on failure.
     *
    * @todo Add explicit public visibility modifier and static declaration matching current usage.
    * @todo Add strict native type hints for the $key parameter and a union return type (array|false) or nullable array.
    * @todo Transition from global legacy database wrapper functions (db_query, db_fetch_array) to PDO or a modern ORM.
    * @todo Use parameterized prepared statements instead of manual string escaping/casting to completely mitigate SQL injection risks.
    */
   static function get_article($key) {

      $query = 'select a.id_article, a.date_created, a.date_modified, a.title, a.content
      from ' . TBL_GLOBAL_ARTICLE . ' a
      where a.id_article = ' . db_int($key) . '';
      $result = db_query( $query );
      return db_fetch_array($result);
   }
}
