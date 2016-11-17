<?php
  class Paginator{
    static $per_page = 10;
    var $total_counts = 0;
    var $records = array();

    function __construct($total_counts, $records){
      $this->total_counts = $total_counts;
      $this->records = $records;
      $this->total_page = ceil($this->total_counts/Paginator::$per_page);
    }

    static function per_page(){
      return Paginator::$per_page;
    }
    static function offset() {
      return (Paginator::current_page()-1) * Paginator::$per_page;
    }

    static function current_page(){
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
      $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

      if($current_page > 1)
        $items[] = $this->link("<<", 1, "prev");

      $offset = intval($current_page - $range/2);
      $upper  = intval($current_page + $range/2);

      $min = $offset >= 1 ? $offset : 1;
      $max = $min + $range <= $this->total_page ? $min + $range : $this->total_page;

      for ($i=$min; $i<= $max; $i++) {
        if($i == $current_page)
          $items[] = "<li class='active'><span> {$current_page}</span></li>";
        else
          $items[] = $this->link($i, $i);;
      }

      if($current_page < $this->total_page)
        $items[] = $this->link(">>", $this->total_page, "next");

      $paging_str = implode(" ", $items);
      return "<div class='pagination'> <ul> {$paging_str}</ul> </div>";
    }

    function link($text, $page, $class=''){
      $form = array();
      parse_str($_SERVER['QUERY_STRING'], $form);
      $form["page"] = $page;

      $url = $_SERVER['PHP_SELF']. "?".http_build_query($form);
      return "<li class='{$class}'><a href='{$url}'> {$text} </a></li> ";
    }

  }
