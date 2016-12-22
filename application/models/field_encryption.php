<?php
class FieldEncryption {
  static function secret_key(){
    //"bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3"
    return $GLOBALS['CFG']->config['secret_key']; //$config["secret_key"];
  }

  //to create a unique secretkey for config['secret_key']
  static function generate_key(){
    $hex = "";
    $string = base64_encode(uniqid()."-nchads-".uniqid());

    for ($i=0; $i < strlen($string); $i++)
      $hex .= dechex(ord($string[$i]));

    return substr($hex, 0, 64);
  }

  static function encrypt($value){
    $key = pack('H*', self::secret_key());

    # show key size use either 16, 24 or 32 byte keys for AES-128, 192
    # and 256 respectively
    $key_size =  strlen($key);

    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    # creates a cipher text compatible with AES (Rijndael block size = 128)
    # to keep the text confidential
    # only suitable for encoded input that never ends with value 00h
    # (because of default zero padding)
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, $iv);

    # prepend the IV for it to be available for decryption
    $ciphertext = $iv . $ciphertext;

    # encode the resulting cipher text so it can be represented by a string
    $ciphertext_base64 = base64_encode($ciphertext);
    return $ciphertext_base64;
  }

  static function decrypt($ciphertext_base64) {
    $key = pack('H*', self::secret_key());
    $ciphertext_dec = base64_decode($ciphertext_base64);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

    # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);

    # retrieves the cipher text (everything except the $iv_size in the front)
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    # may remove 00h valued characters from end of plain text
    $value = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                    $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

    return $value;

  }
}
