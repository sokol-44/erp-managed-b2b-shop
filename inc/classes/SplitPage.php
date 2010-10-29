<?php
/**
 * Data.php Global initialization file
 * Copyright Michał Sokołowski 2010
 *
 * @author Michał Sokołowski <msokolowski@example.com>
 */

if( !defined('_I_INIT') ) die();


class SplitPage {
   var $sql_query, $number_of_rows, $page_number, $page_holder;
   var $number_of_pages, $rows_per_page;
   static $class;

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

   static function g_global() {
      if(self::$class == false) {
         //FIXME error
         self::$class = new SplitPage();
      }
      return self::$class;
   }


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

   // display split-page-number-links
   function display_links($parameters = '', $link_param = '') {
      //      print_debug($this);
      if( $this->number_of_pages > 1 ) {
         return $this->make_links($parameters, $link_param);
      }
   }

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

   // display number of total products found
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
?>