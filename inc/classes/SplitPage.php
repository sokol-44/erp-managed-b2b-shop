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
 * Class SplitPage
 *
 * Handles pagination logic, SQL query modification for limits, and generation of pagination links.
 *
 * @designPattern Strategy / Facade
 *
 * @todo Upgrade class properties to use modern visibility modifiers (public, protected, private) instead of 'var'.
 * @todo Implement strict typing (declare(strict_types=1)) and type hints for all properties and methods.
 * @todo Refactor static instance tracking (self::$class) to use a proper Dependency Injection container instead of a pseudo-singleton.
 * @todo Replace direct global constants and custom procedural global DB function calls with standalone infrastructure/repository pattern components.
 */
class SplitPage {
   /**
    * @var string The modified SQL query with LIMIT clause.
    */
   public $sql_query;

   /**
    * @var int Total number of rows found by the query.
    */
   public $number_of_rows;

   /**
    * @var int Current page number.
    */
   public $page_number;

   /**
    * @var string The query parameter name used to hold the page number.
    */
   public $page_holder;

   /**
    * @var int Total number of pages.
    */
   public $number_of_pages;

   /**
    * @var int Number of rows to display per page.
    */
   public $rows_per_page;

   /**
    * @var SplitPage|null Static instance of the SplitPage class.
    */
   static $class;

   /**
    * SplitPage constructor.
    *
    * Initializes the pagination class with the page holder parameter name and maximum rows per page.
    *
    * @param string $page_holder The query parameter name for the page number. Defaults to 'page'.
    * @param int $max_rows The maximum number of rows per page. Defaults to 0.
    * @return void
    *
    * @todo Use constructor property promotion (PHP 8.0+) and strict typing.
    * @todo Avoid accessing global-like Framework class directly; inject dependencies instead.
    */
   function __construct($page_holder = 'page', $max_rows = 0) {

      $F = Framework::g_global();
      $this->page_holder = 'SP_' . $page_holder;

      if ( $F->not_null($F->GET[$this->page_holder]) && $F->GET[$this->page_holder] > 0 ) {
         $this->page_number = (int)$F->GET[$this->page_holder];
      } else {
         $this->page_number = 1;
      }

      if( $max_rows > 0 ) {
         $this->rows_per_page = $max_rows;
      } elseif ( defined('DEFAULT_MAX_ROWS') && (int)constant('DEFAULT_MAX_ROWS') > 0 ) {
         $this->rows_per_page = (int)constant('DEFAULT_MAX_ROWS');
      } else {
         $this->rows_per_page = 10;
      }

      self::$class = $this;
   }

   /**
    * Returns the global static instance of SplitPage.
    *
    * @return SplitPage The active SplitPage instance.
    *
    * @todo Replace this static locator locator pattern with proper Dependency Injection container setups.
    */
   static function g_global() {
      if(self::$class == false) {
         //FIXME error
         self::$class = new SplitPage();
      }
      return self::$class;
   }

   /**
    * Prepares the SQL query by appending the LIMIT clause based on current page and rows per page.
    *
    * Calculates total rows and total pages using an embedded count query wrapper strategy.
    *
    * @param string $query The original SQL query.
    * @param string|bool $count_query Optional custom count query. Defaults to false.
    * @return string The modified SQL query with LIMIT clause.
    *
    * @todo Use prepared statements and PDO instead of custom global db functions to prevent SQL injection vulnerabilities.
    * @todo Add return type hint 'string'.
    */
   function prepare_sql($query, $count_query = false) {

      $F = Framework::g_global();

      if( $F->not_null($count_query) ) {
         if( strstr($count_query, 'as total') ) {
            $count = db_fetch_array( db_query($count_query) );
         } else {
            $count = db_fetch_array( db_query("select count(*) as total from (" . $count_query . " ) as count") );
         }
      } else {
         $count = db_fetch_array( db_query("select count(*) as total from (" . $query . " ) as count") );
      }

      if( $count ) {
         $this->number_of_rows = (int)$count['total'];
      } else {
         $this->number_of_rows = 0;
      }

      $this->number_of_pages = ceil($this->number_of_rows / $this->rows_per_page);

      if ($this->page_number > $this->number_of_pages) {
         $this->page_number = $this->number_of_pages;
      }

      $offset = ($this->rows_per_page * ($this->page_number - 1));

      $this->sql_query = $query . ' limit ' . max($offset, 0) . ', ' . $this->rows_per_page;
      return $this->sql_query;
   }

   /**
    * Displays split-page-number-links if there is more than one page.
    *
    * @param string $parameters Additional URL parameters string context. Defaults to ''.
    * @param string $link_param Additional HTML element link parameters/attributes. Defaults to ''.
    * @return string|null The HTML string for pagination links, or null if only one page exists.
    *
    * @todo Add return type hint '?string' / 'string|null'.
    */
   function display_links($parameters = '', $link_param = '') {
      //      print_debug($this);
      if( $this->number_of_pages > 1 ) {
         return $this->make_links($parameters, $link_param);
      }
   }

   /**
    * Generates the HTML markup for pagination links.
    *
    * @param string $parameters Additional URL parameters. Defaults to ''.
    * @param string $link_param Additional link parameters/attributes. Defaults to ''.
    * @return string The HTML markup container block for pagination links.
    *
    * @todo Avoid using global variables ($PHP_SELF, $request_type).
    * @todo Use a template engine rendering system or PSR-7 UriInterface instead of building manual raw HTML generation loops.
    * @todo Add return type hint 'string'.
    */
   function make_links($parameters = '', $link_param = '') {

      $F = Framework::g_global();
      global $PHP_SELF, $request_type;

      $display_links_string = '<div class="SplitPageContainer">';

      if ( $F->not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';
      $local_GET = $F->make_get();
      //      print_r($local_GET);
      //prev button - not on first page
      if ($this->page_number > 1) {
         $display_links_string .= '<div class="SplitPagePrev">';
         $get = $F->add_local_get($this->page_holder, $this->page_number - 1);
         $display_links = $F->make_link($F->com, $get);
         $display_links_string .= $F->draw_link($display_links, $link_param, Lang::_('SPLITPAGE_BUTTON_PREV')) . '</div>';
      }

      //if number_of_pages > $max_page_links
      $cur_window_num = intval($this->page_number / $this->rows_per_page);
      if ($this->page_number % $this->rows_per_page) $cur_window_num++;

      $max_window_num = intval($this->number_of_pages / $this->rows_per_page);
      if ($this->number_of_pages % $this->rows_per_page) $max_window_num++;

      //prev bunch
      if ($cur_window_num > 1) {
         $display_links_string .= '<div class="SplitPageGroup">';
         $get = $F->add_local_get($this->page_holder, (($cur_window_num - 1) * $this->rows_per_page) - 1);
         $display_links = $F->make_link($F->com, $get);
         $display_links_string .= $F->draw_link($display_links, $link_param, '...') . '</div>';
      }

      //pages list
      $list_start = 1 + (($cur_window_num - 1) * $this->rows_per_page);
      $list_count_end1 = $cur_window_num * $this->rows_per_page;
      $list_count_end2 = $this->number_of_pages;
      for ($jump_to_page = $list_start; ($jump_to_page <=  $list_count_end1 && $jump_to_page <= $list_count_end2); $jump_to_page++) {
         if ($jump_to_page == $this->page_number) {
            $display_links_string .= '<div class="SplitPageThis">' . $jump_to_page . '</div>';
         } else {
            $display_links_string .= '<div class="SplitPagePage">';
            $get = $F->add_local_get($this->page_holder, $jump_to_page);
            $display_links = $F->make_link($F->com, $get);
            $display_links_string .= $F->draw_link($display_links, '', $jump_to_page) . '</div>';
         }
      }
      //$address = '', $parameters = '', $contents = '', $target = '')
      //next bunch
      if ($cur_window_num < $max_window_num) {
         $display_links_string .= '<div class="SplitPageGroup">';
         $get = $F->add_local_get($this->page_holder, (($cur_window_num) * $this->rows_per_page + 2));
         $display_links = $F->make_link($F->com, $get);
         $display_links_string .= $F->draw_link($display_links, $link_param, '...') . '</div>';
      }

      //next
      if (($this->page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) {
         $display_links_string .= '<div class="SplitPageNext">';
         $get = $F->add_local_get($this->page_holder, ($this->page_number + 1));
         $display_links = $F->make_link($F->com, $get);
         $display_links_string .= $F->draw_link($display_links, $link_param, Lang::_('SPLITPAGE_BUTTON_NEXT')) . '</div>';
      }
      $display_links_string .= '</div>';

      return $display_links_string;
   }

   /**
    * Displays mobile split-page-number-links if there is more than one page.
    *
    * @param string $parameters Additional URL parameters. Defaults to ''.
    * @param string $link_param Additional link parameters/attributes. Defaults to ''.
    * @return string|null The HTML string for pagination links, or null if only one page exists.
    *
    * @todo Add return type hint '?string'.
    * @todo Refactor to avoid duplicate copy-paste logic routines mapping against standard display_links().
    */
   function display_links_m($parameters = '', $link_param = '') {
       //      print_debug($this);
       if( $this->number_of_pages > 1 ) {
           return $this->make_links($parameters, $link_param);
       }
   }

   /**
    * Generates the HTML markup for mobile pagination links (using list items structure).
    *
    * @param string $elements Unused tracking parameter placeholder context. Defaults to ''.
    * @param string $link_param Additional link parameters/attributes. Defaults to ''.
    * @return string The HTML markup unordered list tracking mobile pagination links.
    *
    * @todo Fix undefined variable notice $parameters which is evaluated inside logic but omitted from signature parameters.
    * @todo Avoid using legacy global variables ($PHP_SELF, $request_type).
    * @todo Use a template rendering engine engine instead of raw manual string append loops.
    * @todo Add return type hint 'string'.
    */
   function make_links_m($elements = '', $link_param = '') {

       $F = Framework::g_global();
       global $PHP_SELF, $request_type;

       $display_links_string = '<ul>';

       if ( $F->not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';
       $local_GET = $F->make_get();
       //      print_r($local_GET);
       //prev button - not on first page
       if ($this->page_number > 1) {
           $display_links_string .= '<li class="istFirstItem">';
           $get = $F->add_local_get($this->page_holder, $this->page_number - 1);
           $display_links = $F->make_link($F->com, $get);
           $display_links_string .= $F->draw_link($display_links, $link_param, Lang::_('SPLITPAGE_BUTTON_PREV')) . '</li>';
       }

       //if number_of_pages > $max_page_links
       $cur_window_num = intval($this->page_number / $this->rows_per_page);
       if ($this->page_number % $this->rows_per_page) $cur_window_num++;

       $max_window_num = intval($this->number_of_pages / $this->rows_per_page);
       if ($this->number_of_pages % $this->rows_per_page) $max_window_num++;

       //prev bunch
       if ($cur_window_num > 1) {
           $display_links_string .= '<li class="istFirstItem">';
           $get = $F->add_local_get($this->page_holder, (($cur_window_num - 1) * $this->rows_per_page) - 1);
           $display_links = $F->make_link($F->com, $get);
           $display_links_string .= $F->draw_link($display_links, $link_param, '...') . '</li>';
       }

       //pages list
       $list_start = 1 + (($cur_window_num - 1) * $this->rows_per_page);
       $list_count_end1 = $cur_window_num * $this->rows_per_page;
       $list_count_end2 = $this->number_of_pages;
       for ($jump_to_page = $list_start; ($jump_to_page <=  $list_count_end1 && $jump_to_page <= $list_count_end2); $jump_to_page++) {
           if ($jump_to_page == $this->page_number) {
               $display_links_string .= '<li>' . $jump_to_page . '</li>';
           } else {
               $display_links_string .= '<li>';
               $get = $F->add_local_get($this->page_holder, $jump_to_page);
               $display_links = $F->make_link($F->com, $get);
               $display_links_string .= $F->draw_link($display_links, '', $jump_to_page) . '</li>';
           }
       }
       //$address = '', $parameters = '', $contents = '', $target = '')
       //next bunch
       if ($cur_window_num < $max_window_num) {
           $display_links_string .= '<li>';
           $get = $F->add_local_get($this->page_holder, (($cur_window_num) * $this->rows_per_page + 2));
           $display_links = $F->make_link($F->com, $get);
           $display_links_string .= $F->draw_link($display_links, $link_param, '...') . '</li>';
       }

       //next
       if (($this->page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) {
           $display_links_string .= '<li>';
           $get = $F->add_local_get($this->page_holder, ($this->page_number + 1));
           $display_links = $F->make_link($F->com, $get);
           $display_links_string .= $F->draw_link($display_links, $link_param, Lang::_('SPLITPAGE_BUTTON_NEXT')) . '</li>';
       }
       $display_links_string .= '</ul>';

       return $display_links_string;
   }

   /**
    * Displays the template text block demonstrating current element metrics boundaries out of total indicators.
    *
    * @param string $text_output Format translation layout string tracking inputs (e.g., "Showing %d to %d of %d").
    * @return string Converted string mapping formatted metrics data.
    *
    * @todo Add formal scalar syntax type hinting for parameters and return types.
    */
   function display_count($text_output) {
      $to_num = ($this->rows_per_page * $this->page_number);
      if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

      $from_num = ($this->rows_per_page * ($this->page_number - 1));

      if ($to_num == 0) {
         $from_num = 0;
      } else {
         $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
   }
}
