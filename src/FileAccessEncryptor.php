<?php

namespace MSL;
//====================================================================================================================================
//                                                                                                                                    
//  #####   ##    ##        ###    ###   ####   #####   ##      ##   ##  ######  ##   #####   ##     ##   ####      ##      ##  ##  
//  ##  ##   ##  ##         ## #  # ##  ##     ##   ##  ##      ##   ##    ##    ##  ##   ##  ####   ##  ##         ##      ## ##   
//  #####     ####          ##  ##  ##   ###   ##   ##  ##      ##   ##    ##    ##  ##   ##  ##  ## ##   ###       ##      ####    
//  ##  ##     ##           ##      ##     ##  ##   ##  ##      ##   ##    ##    ##  ##   ##  ##    ###     ##      ##      ## ##   
//  #####      ##           ##      ##  ####    #####   ######   #####     ##    ##   #####   ##     ##  ####   ##  ######  ##  ##  
//                                                                                                                                    
//====================================================================================================================================

/*******************************
 * V 1.0 FILE ACCESS ENCRYPTOR *
 *    ALRIGHTS RECEIVED BY MM  *
 *       DEVELOPED BY MM       *
 *         2022-05-18          *
 *******************************/

use Closure;

class FileAccessEncryptor
{

     /****************************
     *  ENCRYPTION DECRYPTION   *
     * FORMULAR DEVELOPED BY MM *
     ****************************/
    private static $ENCRYPTION_KEY = null;
    private static $ENCRYPTION_ALGORITHM = 'AES-256-CBC';
    private static $SEPARATOR = '--::MM::--';
    private static $TYPE = 'pdf';
    private static $NAME = '';
    private static $MUTATE_FUNCTION = false;

    public function __construct($key = false)
    {
        self::$ENCRYPTION_KEY = $key ?? getenv("APP_KEY");
    }

    public static function type($type)
    {
        self::$TYPE = $type;
    }

    public static function name($name)
    {
        self::$NAME = $name;
    }

    public static function mutateRealPath(Closure $mutate_function)
    {
        self::$MUTATE_FUNCTION = $mutate_function;
    }

    public static function encryptRealPath($realPath, $duration)
    {
        $expiry = time() + $duration;
        $complete_input = $realPath . self::$SEPARATOR . $expiry;
        $enc = self::Encrypt($complete_input);
        return $enc;
    }

    public static function decryptRealPath($encrypted)
    {
        $time = time();
        $decrypt = self::Decrypt($encrypted);
        $function = self::$MUTATE_FUNCTION;
        if (!strpos($decrypt, self::$SEPARATOR)) {
            return ["status" => "expired", "path" => null];
        }
        $explode = explode(self::$SEPARATOR, $decrypt);
        $realPath = !empty($explode[0]) ? $explode[0] : '0';
        $expiry = !empty($explode[1]) ? $explode[1] : '0';
        if ($expiry > $time) {
            return ["status" => "valid", "path" => $function ? $function($realPath) : $realPath];
        }
        return ["status" => "expired", "path" => null];
    }

    public static function preview($encrypted)
    {
        $result = self::decryptRealPath($encrypted);
        if ($result['status'] == "valid") {
            self::view($result['path']);
        }else{
            abort(404);
        }
    }

    private static function view($real)
    {
        switch (self::$TYPE) {
            case "pdf":
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=" . self::$NAME . "." . self::$TYPE);
                @readfile($real);
                break;
            case "jpg":
                header("Content-type: image/jpg");
                header("Content-Disposition: inline; filename=" . self::$NAME . "." . self::$TYPE);
                @readfile($real);
                break;
            case "jpeg":
                header("Content-type: image/jpeg");
                header("Content-Disposition: inline; filename=" . self::$NAME . "." . self::$TYPE);
                @readfile($real);
                break;
        }
    }
    private static function Encrypt($ClearTextData)
    {
        $EncryptionKey = base64_decode(self::$ENCRYPTION_KEY);
        $InitializationVector  = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$ENCRYPTION_ALGORITHM));
        $EncryptedText = openssl_encrypt($ClearTextData, self::$ENCRYPTION_ALGORITHM, $EncryptionKey, 0, $InitializationVector);
        return self::base64_special_encode($EncryptedText . '::' . $InitializationVector);
    }

    private static function Decrypt($CipherData)
    {
        $EncryptionKey = base64_decode(self::$ENCRYPTION_KEY);
        list($Encrypted_Data, $InitializationVector) = array_pad(explode('::', self::base64_special_decode($CipherData), 2), 2, null);
        return openssl_decrypt($Encrypted_Data, self::$ENCRYPTION_ALGORITHM, $EncryptionKey, 0, $InitializationVector);
    }

    private static function base64_special_encode($enc)
    {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($enc));
    }

    private static function base64_special_decode($enc)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $enc));
    }
   
}
