<?php
/**
 * Convert a postgres date to php date
 * @param $mysql_date
 */
function content_hidden($content){
  $hide = str_repeat("&#x2022;", strlen($content));
  $content = <<<EOT
  <span class='content-hidden' data-hidden-content="{$content}">
    $hide
  </span>
EOT;
  echo $content;
}

function mysql_to_date($postgres_date) {
  if ($postgres_date == NULL || $postgres_date == "") {
    return NULL;
  }

  $arr = explode("-", $postgres_date);
  $result =  new DateTime();
  $result->setDate($arr[0], $arr[1], $arr[2]);
  return $result;
}


function mysql_datetime_to_date($postgres_datetime = "") {
    if ($postgres_datetime == NULL || $postgres_datetime == "") {
    return NULL;
  }
  $arr = explode(" ", $postgres_datetime);
  $postgres_date = $arr[0];
  $datetime = "00:00:00";
  if (count($arr) > 1) :
    $datetime_arr = explode(".", $arr[1]);
    $datetime = $datetime_arr[0];
  endif;
  $arr = explode("-", $postgres_date);
  $arr2 = explode(":", $datetime);
  $result =  new DateTime();
  $result->setDate($arr[0], $arr[1], $arr[2]);
  $result->setTime($arr2[0], $arr2[1], $arr2[2]);
  return $result;

}

/**
 * Convert PHP date to string with a specific format
 * @param $php_date
 * @param $format
 */
function date_to_date_format($php_date, $format=Iconstant::APP_DATE_FORMAT) {
  $formats = array("Y-M-D",
                   "M/D/Y",
                   "D/M/Y"
             );
  $format = strtoupper($format);
  if ($php_date == NULL || !in_array($format, $formats)) {
    return NULL;
  }

    if ($format == "Y-M-D") {
      return $php_date->format("Y-m-d");
    } else if ($format == "D/M/Y") {
      return $php_date->format("d/m/Y");
    } else if ($format == "M/D/Y") {
      return $php_date->format("m/d/Y");
    } else {
      return NULL;
    }
}

function date_to_date_format_hide_year($php_date, $format=Iconstant::APP_DATE_FORMAT) {
  $formats = array("Y-M-D",
                   "M/D/Y",
                   "D/M/Y"
             );
  $format = strtoupper($format);
  if ($php_date == NULL || !in_array($format, $formats)) {
    return NULL;
  }

    if ($format == "Y-M-D") {
      return $php_date->format("m-d");
    } else if ($format == "D/M/Y") {
      return $php_date->format("d/m");
    } else if ($format == "M/D/Y") {
      return $php_date->format("m/d");
    } else {
      return NULL;
    }
}

/**
 * Convert mysql date to html date
 * @param $postgres_date
 * @param $format
 */
function date_mysql_to_html($postg_date, $format=Iconstant::APP_DATE_FORMAT) {
  $arr = explode(" ", $postg_date);
  $postgres_date = $arr[0];
  $php_date = mysql_to_date($postgres_date);
  return date_to_date_format($php_date, $format);
}

function datetime_mysql_to_html($postgres_datetime, $format=Iconstant::APP_DATE_FORMAT) {
  if ($postgres_datetime == NULL || $postgres_datetime == "") {
    return NULL;
  }
  $arr = explode(" ", $postgres_datetime);
  $postgres_date = $arr[0];
  $datetime = "00:00:00";
  if (count($arr) > 1) :
    $datetime_arr = explode(".", $arr[1]);
    $datetime = $datetime_arr[0];
  endif;
  $php_date = mysql_to_date($postgres_date);
  return date_to_date_format($php_date, $format)." ".$datetime;
}

function date_from_str($str_date, $format){
  $parses = date_parse_from_format($format, $str_date);
  if($parses){
    $year = $parses['year'];
    $month = $parses['month'];
    $day = $parses['day'];
    $date = "{$year}-${month}-{$day}";
    return $date;
  }
  return NULL;
}

function gparse_date($str_date, $format=Iconstant::APP_DATE_FORMAT) {
  $formats = array('d/m/Y', 'Y-m-d', 'm/d/Y' );
  $date =  date_from_str($str_date, $format);
  if($date)
    return $date;

  foreach($formats as $format){
    $date = date_from_str($str_date, $format);
    if($date)
      return $date;
  }

  return NULL;

  // $format = strtoupper($format);
  // if ($str_date ==NULL || $str_date=="" || !in_array($format, $formats)) {
  //   return NULL;
  // }
  //
  // if ($format == "Y-M-D") {
  //   $arr = explode("-", $str_date);
  //   if (count($arr) != 3) { return NULL;}
  //   $d = $arr[2];
  //   $m = $arr[1];
  //   $y = $arr[0];
  // } else if ($format == "D/M/Y") {
  //   $arr = explode("/", $str_date);
  //   if (count($arr) != 3) { return NULL;}
  //   $d = $arr[0];
  //   $m = $arr[1];
  //   $y = $arr[2];
  // } else if ($format =="M/D/Y") {
  //   $arr = explode("/", $str_date);
  //   if (count($arr) != 3) { return NULL;}
  //   $d = $arr[1];
  //   $m = $arr[0];
  //   $y = $arr[2];
  // } else {
  //   return NULL;
  // }
  //
  // if (!is_nint($m) || !is_nint($d) || !is_nint($y)) return NULL;
  //
  // if (checkdate($m, $d, $y)) {
  //   $result = new DateTime();
  //   $result->setDate($y, $m, $d);
  //   $result->setTime(0, 0, 0);
  //   return $result;
  // } else {
  //     return NULL;
  // }
}

function datetime_html_to_php($str_date, $time, $format=Iconstantt::APP_DATE_FORMAT) {
    // $php_date = gparse_date($str_date, $format);
    // $arr = explode(":", $time);
    // $h = $arr[0];
    // $m = $arr[1];
    // $s = count($arr) > 2 ? $arr[2] : 0;
    // $php_date->setTime($h, $m, $s);
    // return $php_date;
    gparse_date($str_date, $format)." ".$time;
}


/**
 * Convert date html to mysql
 * @param $html_date
 * @param $format
 */
function date_html_to_mysql($html_date, $format=Iconstant::APP_DATE_FORMAT) {
  // $php_date = gparse_date($html_date, $format);
  // return $php_date==NULL ? NULL :  $php_date->format("Y-m-d");
  return gparse_date($html_date, $format);
}

function date_html_to_php($html_date, $format=Iconstant::APP_DATE_FORMAT) {
    $php_date = gparse_date($html_date, $format);
    return $php_date;
}

/**
 * Detech if the input is an interger
 * @param $input
 */
function is_nint($input){
  return preg_match('@^[0-9]+$@',$input) === 1;
}

function is_nnumeric($input) {
  return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $input);
}

function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);

    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function karm_error($error, $normal=false) {
    if (isset($error) && $error != "") {
      if ($normal==false) {
        echo "<fieldset style=\"width:800px\">";
            echo "<b>".k_lang("common_error_header").":</b>";
            echo "<ul class=\"error_message\">".$error."</ul>";
            echo "</fieldset>";
      } else {
          echo "<span class=\"error_message\">".$error."</span>";
      }
    }
}

function karm_success($success) {
    if (isset($success) && $success != "") {
        echo "<span class=\"success_message\">".$success."</span>";
    }
}

function k_start_with($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function k_end_with($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function generateOrder ($cur_field, $pre_order) {
  if (empty($pre_order)) {
    return Kencryption::encrypt(serialize(array("order"=> $cur_field, "direction"=>"ASC")));
  }
  if ($cur_field != $pre_order["order"]) {
      $marray = array("order" => $cur_field, "direction" => "ASC");
  } else {
    $marray = array();
    $marray["order"] = $cur_field;
      if ($pre_order["direction"] == "ASC") {
        $marray["direction"] = "DESC";
      } else {
          $marray["direction"] = "ASC";
      }
  }
    return Kencryption::encrypt(serialize($marray));
}

function generateOrderImage($cur_field, $pre_order) {
    if (empty($pre_order)) {
    return "";
  }

  $image = "";
    if ($cur_field != $pre_order["order"]) {
      return "&nbsp;&nbsp;";
  } else {
      if ($pre_order["direction"] == "ASC") {
        $image = "<img src=\"".base_url("images/arrow_up.png")."\" width=\"15px\" border=\"0\"/>";
      } else {
          $image = "<img src=\"".base_url("images/arrow_down.png")."\" width=\"15px\" border=\"0\"/>";
      }
  }
  return $image;
}

function k_lang($line, $escapehtml=true, $id = '') {
  $CI =& get_instance();
  $line = $CI->lang->line($line);

  if ($escapehtml) {
      $line = htmlspecialchars($line);
  }

  if ($id != '') {
    $line = '<label for="'.$id.'">'.$line."</label>";
  }
  return $line;
}

function k_decimal_format($number) {
   if ($number == null || $number == "") {
        return null;
   }
   return number_format($number, 2, ".", "");
}

function ktime_format($time) {
  if ($time == null) {
    return null;
  }
   $arr = explode(":", $time);
   return $arr[0].":".$arr[1];
}

/**
 * Getting current url
 */
function cur_page_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
       $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * @param string $a
 * @param string $b
 */
function addSpecial($a, $b) {
    if (is_null($a)) :
        return $b;
     endif;
     if (is_null($b)) :
        return $a;
     endif;
     return $a + $b;
}
