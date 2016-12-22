<?php
  class Paginator{
    static $per_page = 10;
    var $total_counts;
    var $records_per_page;
    var $current_page;
    var $records;

    function __construct($total_counts, $records){
      $this->total_counts = $total_counts;
      $this->records = $records;
      $this->current_page = Paginator::page();
      $this->records_per_page = Paginator::$per_page;
      $this->total_pages = ceil($this->total_counts/Paginator::$per_page);
    }

    static function per_page(){
      return Paginator::$per_page;
    }
    static function offset() {
      return (Paginator::page()-1) * Paginator::$per_page;
    }

    static function page(){
      $page = isset($_GET['page']) && intval($_GET['page']) > 1 ? intval($_GET['page']) : 1 ;
      return $page;
    }

    function required_paginate() {
      return Paginator::$per_page < $this->total_counts;
    }

    function render(){
      if(!$this->required_paginate())
        return "";

      $items = array();
      $range = 6;


      if($this->current_page > 1)
        $items[] = $this->link("<<", 1, "prev");

      $offset = intval($this->current_page - $range/2);
      $upper  = intval($this->current_page + $range/2);

      $min = $offset >= 1 ? $offset : 1;
      $max = $min + $range <= $this->total_pages ? $min + $range : $this->total_pages;

      for ($i=$min; $i<= $max; $i++) {
        if($i == $this->current_page)
          $items[] = "<li class='active'><span> {$this->current_page}</span></li>";
        else
          $items[] = $this->link($i, $i);;
      }

      if($this->current_page < $this->total_pages)
        $items[] = $this->link(">>", $this->total_pages, "next");

      $items[] = "<li class='active'><span> Total: {$this->total_counts}</span></li>";

      $paging_str = implode(" ", $items);
      return "<div class='pagination'><ul> {$paging_str}</ul> </div>";
    }

    function link($text, $page, $class=''){
      $url = AppHelper::url(array("page" => $page));
      return "<li class='{$class}'><a href='{$url}'> {$text} </a></li> ";
    }

  }
