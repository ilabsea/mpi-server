<?php
class Huskar {
  protected $_variables = array(
      'classes' => array('clearfix', 'pagination'),
      'crumbs' => 5,
      'rpp' => 10,
      'key' => 'page',
      'target' => '',
      'next' => 'Next &raquo;',
      'previous' => '&laquo; Previous',
      'alwaysShowPagination' => false,
      'clean' => false
  );

  public function __construct($current = null, $total = null){
    if (!is_null($current))
      $this->setCurrent($current);

    if (!is_null($total))
        $this->setTotal($total);

    $this->_variables['get'] = $_GET;
  }

  protected function _check(){
    if (!isset($this->_variables['current']))
      throw new Exception('Pagination::current must be set.');
    else if (!isset($this->_variables['total']))
      throw new Exception('Pagination::total must be set.');
  }

  public function addClasses($classes){
    $this->_variables['classes'] = array_merge( $this->_variables['classes'], (array) $classes);
  }

  public function alwaysShowPagination(){
    $this->_variables['alwaysShowPagination'] = true;
  }

  public function getCanonicalUrl(){
    $target = $this->_variables['target'];
    if (empty($target))
      $target = $_SERVER['PHP_SELF'];
    $page = (int) $this->_variables['current'];

    if ($page !== 1)
      return 'http://' . ($_SERVER['HTTP_HOST']) . ($target) . $this->getPageParam();

    return 'http://' . ($_SERVER['HTTP_HOST']) . ($target);
  }

  public function getPageParam($page = false) {
    if ($page === false)
      $page = (int) $this->_variables['current'];
    $key = $this->_variables['key'];
    return '?' . ($key) . '=' . ((int) $page);
  }

  public function getPageUrl($page = false){
    $target = $this->_variables['target'];
    if (empty($target))
      $target = $_SERVER['PHP_SELF'];
    return 'http://' . ($_SERVER['HTTP_HOST']) . ($target) . ($this->getPageParam($page));
  }

  public function getRelPrevNextLinkTags(){
    $target = $this->_variables['target'];
    if (empty($target))
      $target = $_SERVER['PHP_SELF'];

    $key = $this->_variables['key'];
    $params = $this->_variables['get'];
    $params[$key] = 'pgnmbr';
    $href = ($target) . '?' . http_build_query($params);
    $href = preg_replace(
      array('/=$/', '/=&/'),
      array('', '&'),
      $href
    );
    $href = 'http://' . ($_SERVER['HTTP_HOST']) . $href;

    // Pages
    $currentPage = (int) $this->_variables['current'];
    $numberOfPages = ( (int) ceil($this->_variables['total'] / $this->_variables['rpp']));


    if ($currentPage === 1) {
      if ($numberOfPages > 1) {
          $href = str_replace('pgnmbr', 2, $href);
          return array( '<link rel="next" href="' . ($href) . '" />' );
      }
      return array();
    }

    // Store em
    $prevNextTags = array(
        '<link rel="prev" href="' . (str_replace('pgnmbr', $currentPage - 1, $href)) . '" />'
    );

    // There is a page after this one
    if ($numberOfPages > $currentPage) {
      array_push(
          $prevNextTags,
          '<link rel="next" href="' . (str_replace('pgnmbr', $currentPage + 1, $href)) . '" />'
      );
    }
    return $prevNextTags;
  }

  public function parse() {
    $this->_check();

    foreach ($this->_variables as $_name => $_value) {
      $$_name = $_value;
    }

    ob_start();
    include 'render.inc.php';
    $_response = ob_get_contents();
    ob_end_clean();
    return $_response;
  }

  public function setClasses($classes) {
    $this->_variables['classes'] = (array) $classes;
  }

  public function setClean(){
    $this->_variables['clean'] = true;
  }

  public function setCrumbs($crumbs){
    $this->_variables['crumbs'] = $crumbs;
  }

  public function setCurrent($current){
    $this->_variables['current'] = $current;
  }

  public function setFull(){
    $this->_variables['clean'] = false;
  }

  public function setKey($key){
    $this->_variables['key'] = $key;
  }

  public function setNext($str){
    $this->_variables['next'] = $str;
  }

  public function setPrevious($str){
    $this->_variables['previous'] = $str;
  }

  public function setRPP($rpp){
    $this->_variables['rpp'] = $rpp;
  }

  public function setTarget($target){
    $this->_variables['target'] = $target;
  }

  public function setTotal($total){
    $this->_variables['total'] = $total;
  }
}
