<?php

/**
 * *****************************************************************
 * Copyright 2019.
 * All Rights Reserved to
 * Nagad
 * Redistribution or Using any part of source code or binary
 * can not be done without permission of Nagad
 * *****************************************************************
 *
 * @author - Md Nazmul Hasan Nazim
 * @email - nazmul.nazim@nagad.com.bd
 * @date: 18/11/2019
 * @time: 10:20 AM
 * ****************************************************************
 */

 

function generateRandomString($length = 40)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function EncryptDataWithPublicKey($data)
{
    $pgPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiCWvxDZZesS1g1lQfilVt8l3X5aMbXg5WOCYdG7q5C+Qevw0upm3tyYiKIwzXbqexnPNTHwRU7Ul7t8jP6nNVS/jLm35WFy6G9qRyXqMc1dHlwjpYwRNovLc12iTn1C5lCqIfiT+B/O/py1eIwNXgqQf39GDMJ3SesonowWioMJNXm3o80wscLMwjeezYGsyHcrnyYI2LnwfIMTSVN4T92Yy77SmE8xPydcdkgUaFxhK16qCGXMV3mF/VFx67LpZm8Sw3v135hxYX8wG1tCBKlL4psJF4+9vSy4W+8R5ieeqhrvRH+2MKLiKbDnewzKonFLbn2aKNrJefXYY7klaawIDAQAB";
    $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";

    // echo $public_key; 
    // exit();
    $key_resource = openssl_get_publickey($public_key);
    openssl_public_encrypt($data, $cryptText, $key_resource);
    return base64_encode($cryptText);
}

function SignatureGenerate($data)
{
    $merchantPrivateKey = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCOWZJ1fZqWCfGYvBCUebhSwBqVFQZDOFdPVbD9XJNywB4nETclmefdvXJzEzpL1SKt18HcedDbmMTNLCrVL4JLEG6L2G095INbOoLicidUSMAckOKBs8fAI2HpATmaEuF21duALIg1eDC0PFZfNEDX1DqyDZfQzdLDHtWpV1cxQicOy3wco26kY2TGwI2GWl0eatZyKCcFdZAkygCNVJA/NZ18Njieck2g/x3hb1YgPCSlWOzR//O+svfZ0r5u2x7rR7VGHvi7n0zm9cRESg2z2qnO6oSxOYm/MR63CmP7wDV/8COMj6NWeq61MIXohUFsvDscKcVKuJiobMS4kYaRAgMBAAECggEAMytTkFRUKzbr16FSvGx8q5JOe+SQ2bz49ZWyB4Etp4QT0qtHjYajaHvfFiqFPBkjXCZAk4ZeY3Go3K/RvcxUI7OGaVKlAiiQZ0zDG685H/z+wcX7BfCgDxso6N7927qJvXPmcU6Qr9fCeiduTM61uF+bscFdHzu9VVJSL4sr1pKLlaWzXGLEhIvg6sNYVDhxSVCTsuQAxF8sY8jkmz/TeQcGZoYp+CvhQvj6+RJbI9iZHvgQ2q2R6Ccy7zjSya4j0c/ETcdWJPssCr7ndwIk+o1y5ICra6XHGLcnyWY2cH6YVPhOCJ/uLtrahK6sRiGgSSDOpkTxjIgZVSzGiVJvgQKBgQDkKWRWxn98CZLxEqpPNIP4VqxNu0HtELePiYefEX4iYJCXrDEAYaYICS3JrEaH1EZRcDycm8xDNte9U0z90a142jOgoHopleCTPQIQBC7vNKXrfSf/PAlPu5cNAyT1Htn7th/+alSniTlbku7r9cKUxU0XoO2xl1YHE19QMzn6OQKBgQCft9to3KBiaOjdV+8An2sDaph3rlx7iz97m7Om68hcFRT0+VRu6mLC6+FvIpAPUkmeh6njS+5PQQ7bFrSs1Lgj/d7VBBoVoEPEZrKsxmbtES+ZPg1fZheduPG3HG9QDwcI08gobVeTg/VOtIg5o1NDdIs+F+UUUKGkG24hGzTPGQKBgQDC48hdhvhmTxdA7JylQ0pm2kIbs5RAl0L0TJmX+i0v2CQqKl9A4CgePRjrZ0hnxZ66+ZAMhdgYocPuaDUfqfbvgBMRZYJz+x31JuuYGrqsgOApIqMDlZiujqIb70+mQVAZfhweCV1+Lezcl8mVrDslMNMFF+VxCL/nE1ka/GbNoQKBgQCQEJ0eJvOcEJkLHkf8vc8fE8VXHrVY0q9+ZWFUzRS6ymFO3p7kNRLEU1PSlNBwoX66OhBpb5ITdtFZmU97HZzDCjk21y5YF2lI/IFqCsPITloq1afmRwZCRBHzYvictBFaSv0eShJWgw5/4aOGqeVu0O7QIWmGO3yxs/RQdGPb8QKBgDrYhjDgK6p47KH2krxbjIu2Owp60txY/P7DL7PbGlB1josvUdTfDB8WDDw2vh7hXSKREMaN/AxNvrL0pVUlrCN4Wb7lHzwNLJFUmOV+RDclPcv77Jm6siu3r75VwR/PABoA/S3GZyW4HsxR3skpGeUs97MU8AWWaF5+RzPmNoga";
    $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
    // echo $private_key; 
    // exit();
    openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
    return base64_encode($signature);
}

function HttpPostMethod($PostURL, $PostData)
{
    $url = curl_init($PostURL);
    $postToken = json_encode($PostData);
    $header = array(
        'Content-Type:application/json',
        'X-KM-Api-Version:v-0.2.0',
        'X-KM-IP-V4:' . get_client_ip(),
        'X-KM-Client-Type:PC_WEB'
    );
    
    curl_setopt($url, CURLOPT_HTTPHEADER, $header);
    curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
    curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false); 
    
    $resultData = curl_exec($url);
    $ResultArray = json_decode($resultData, true);
    curl_close($url);
    return $ResultArray;

}

function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function DecryptDataWithPrivateKey($cryptText)
{
    $merchantPrivateKey = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCOWZJ1fZqWCfGYvBCUebhSwBqVFQZDOFdPVbD9XJNywB4nETclmefdvXJzEzpL1SKt18HcedDbmMTNLCrVL4JLEG6L2G095INbOoLicidUSMAckOKBs8fAI2HpATmaEuF21duALIg1eDC0PFZfNEDX1DqyDZfQzdLDHtWpV1cxQicOy3wco26kY2TGwI2GWl0eatZyKCcFdZAkygCNVJA/NZ18Njieck2g/x3hb1YgPCSlWOzR//O+svfZ0r5u2x7rR7VGHvi7n0zm9cRESg2z2qnO6oSxOYm/MR63CmP7wDV/8COMj6NWeq61MIXohUFsvDscKcVKuJiobMS4kYaRAgMBAAECggEAMytTkFRUKzbr16FSvGx8q5JOe+SQ2bz49ZWyB4Etp4QT0qtHjYajaHvfFiqFPBkjXCZAk4ZeY3Go3K/RvcxUI7OGaVKlAiiQZ0zDG685H/z+wcX7BfCgDxso6N7927qJvXPmcU6Qr9fCeiduTM61uF+bscFdHzu9VVJSL4sr1pKLlaWzXGLEhIvg6sNYVDhxSVCTsuQAxF8sY8jkmz/TeQcGZoYp+CvhQvj6+RJbI9iZHvgQ2q2R6Ccy7zjSya4j0c/ETcdWJPssCr7ndwIk+o1y5ICra6XHGLcnyWY2cH6YVPhOCJ/uLtrahK6sRiGgSSDOpkTxjIgZVSzGiVJvgQKBgQDkKWRWxn98CZLxEqpPNIP4VqxNu0HtELePiYefEX4iYJCXrDEAYaYICS3JrEaH1EZRcDycm8xDNte9U0z90a142jOgoHopleCTPQIQBC7vNKXrfSf/PAlPu5cNAyT1Htn7th/+alSniTlbku7r9cKUxU0XoO2xl1YHE19QMzn6OQKBgQCft9to3KBiaOjdV+8An2sDaph3rlx7iz97m7Om68hcFRT0+VRu6mLC6+FvIpAPUkmeh6njS+5PQQ7bFrSs1Lgj/d7VBBoVoEPEZrKsxmbtES+ZPg1fZheduPG3HG9QDwcI08gobVeTg/VOtIg5o1NDdIs+F+UUUKGkG24hGzTPGQKBgQDC48hdhvhmTxdA7JylQ0pm2kIbs5RAl0L0TJmX+i0v2CQqKl9A4CgePRjrZ0hnxZ66+ZAMhdgYocPuaDUfqfbvgBMRZYJz+x31JuuYGrqsgOApIqMDlZiujqIb70+mQVAZfhweCV1+Lezcl8mVrDslMNMFF+VxCL/nE1ka/GbNoQKBgQCQEJ0eJvOcEJkLHkf8vc8fE8VXHrVY0q9+ZWFUzRS6ymFO3p7kNRLEU1PSlNBwoX66OhBpb5ITdtFZmU97HZzDCjk21y5YF2lI/IFqCsPITloq1afmRwZCRBHzYvictBFaSv0eShJWgw5/4aOGqeVu0O7QIWmGO3yxs/RQdGPb8QKBgDrYhjDgK6p47KH2krxbjIu2Owp60txY/P7DL7PbGlB1josvUdTfDB8WDDw2vh7hXSKREMaN/AxNvrL0pVUlrCN4Wb7lHzwNLJFUmOV+RDclPcv77Jm6siu3r75VwR/PABoA/S3GZyW4HsxR3skpGeUs97MU8AWWaF5+RzPmNoga";
    $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
    openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
    return $plain_text;
}

date_default_timezone_set('Asia/Dhaka');

$MerchantID = "689580971105399";
$DateTime = Date('YmdHis');
$amount = "1";
$OrderId = 'TEST'.strtotime("now").rand(1000, 10000);
$random = generateRandomString();    

$PostURL = "https://api.mynagad.com/api/dfs/check-out/initialize/" . $MerchantID . "/" . $OrderId;


//$merchantCallbackURL = "http://nagadcheck.aleshagroup.com/merchant-callback-website.php";
$merchantCallbackURL = "http://aims7.com/temp/merchant-callback-website.php";


$SensitiveData = array(
    'merchantId' => $MerchantID,
    'datetime' => $DateTime,
    'orderId' => $OrderId,
    'challenge' => $random
);

$PostData = array(
    'accountNumber' => '01958097110', //Replace with Merchant Number
    'dateTime' => $DateTime,
    'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveData)),
    'signature' => SignatureGenerate(json_encode($SensitiveData))
);

$Result_Data = HttpPostMethod($PostURL, $PostData);

if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
    if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

        $PlainResponse = json_decode(DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

        if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

            $paymentReferenceId = $PlainResponse['paymentReferenceId'];
            $randomServer = $PlainResponse['challenge'];

            $SensitiveDataOrder = array(
                'merchantId' => $MerchantID,
                'orderId' => $OrderId,
                'currencyCode' => '050',
                'amount' => $amount,
                'challenge' => $randomServer
            );

            $merchantAdditionalInfo = '{"Service Name": "Sheba.xyz"}';

            $PostDataOrder = array(
                'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                'signature' => SignatureGenerate(json_encode($SensitiveDataOrder)),
                'merchantCallbackURL' => $merchantCallbackURL,
                'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
            );

            
            $OrderSubmitUrl = "https://api.mynagad.com/api/dfs/check-out/complete/" . $paymentReferenceId;
            $Result_Data_Order = HttpPostMethod($OrderSubmitUrl, $PostDataOrder);

            // echo json_encode($Result_Data_Order);
            
                if ($Result_Data_Order['status'] == "Success") {
                    $url = json_encode($Result_Data_Order['callBackUrl']);   
                    echo "<script>window.open($url, '_self')</script>";                      
                }
                else {
                    echo json_encode($Result_Data_Order);
                }
        } else {
            echo json_encode($PlainResponse);
        }
    }
}


?>