<?php
/**
 * The encryption class
 * @author Sokha RUM
 */
class Iencryption {
    const ENCRYPTION_TIME = 1;
    
	/**
	 * Encode a string
	 * @param String $str
	 */
	static private function k_encode($str) {
	  for($i=0; $i<Iencryption::ENCRYPTION_TIME; $i++) {
	    $str=strrev(base64_encode($str)); //apply base64 first and then reverse the string
	  }
	  return $str;
	}
	

	/**
	 * Decode a string
	 * @param String $str
	 */
	static private function k_decode($str) {
	  for($i=0; $i<Iencryption::ENCRYPTION_TIME; $i++) {
	    $str=base64_decode(strrev($str)); //apply base64 first and then reverse the string}
	  }
	  return $str;
	}

	
	/**
	 * String to Hexadecimal
	 * @param String $string
	 */
	static private function k_strhex($string) {
	    $hex="";
	    for ($i=0;$i<strlen($string);$i++) {
	        $hex.=(strlen(dechex(ord($string[$i])))<2)? "0".dechex(ord($string[$i])): dechex(ord($string[$i]));
	    }
	    return $hex;
	}

	/**
	 * Hexadecimal to String
	 * @param String $hex
	 */
	static private function k_hexstr($hex) {
	    $string="";
	    for ($i=0;$i<strlen($hex)-1;$i+=2) {
	        $string.=chr(hexdec($hex[$i].$hex[$i+1]));
	    }
	    return $string;
	}
	
	/**
	 * Encrypt a String
	 * @param String $str
	 */
	static function encrypt($str) {
	    return Iencryption::k_strhex(Iencryption::k_encode($str));
	} 
	

	/**
	 * Decrypt a string
	 * @param String $str
	 */
	static function decrypt($str) {
	   return Iencryption::k_decode(Iencryption::k_hexstr($str));
	}
}