# Laravel File Access Encryptor
## Version 1.0


This library will support you to hide/prevent accessing files through the real location

## Features
- Hide your files real location
- URL expiration
- Regenerate secure unique urls

### Installation
```
composer require msolutions/file-access-encryptor
```

### Implementing

```
use MSL\FileAccessEncryptor;

/*initialize the class inside your 
 *constructor or any other function before using
 **/
public function __constructor()
{
    new FileAccessEncryptor();
}

//Generate encrypted token for your files
$realFilePath = '/path/pdf/mypdf.pdf';
$urlExpirationTime = 60; //60 seconds (apply the time in seconds)
$encryptedToken = FileAccessEncryptor::encryptRealPath($realFilePath, $urlExpirationTime);

```

### Decrypting Token
```
use MSL\FileAccessEncryptor;

/*initialize the class inside your 
 *constructor or any other function before using
 **/
public function __constructor()
{
    new FileAccessEncryptor();
}

//decrypt the token and get the real file path
FileAccessEncryptor::name("Give-Some-File-Name"); //optional

//encryption value mutation to get the real url (optional)
FileUrlEncryptor::mutateRealPath(function($value){
            return $value+1;
});

//Preview the file (if need to preview the secure object)
$encryptedToken = 'ENC-TOKEN';
FileAccessEncryptor::type("pdf"); //[pdf, jpg, jpeg] parse here your file type for preview purpose
FileUrlEncryptor::preview($encryptedToken);

//decrypt and get the real file path and expiry status
$encryptedToken = 'ENC-TOKEN';
$result = FileAccessEncryptor::decryptRealPath($encryptedToken);
/*
return ["status" => "valid", "path" => "/path/for-real-file.pdf"];

OR

return ["status" => "expired", "path" => null];
*/

```

This open source package is developed for general use, any of developers can use this for free.
- Please share your comments and ideas to improve the package.
# FileAccessEncryptor
