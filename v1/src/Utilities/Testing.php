<?php
/**
 * Created by PhpStorm.
 * User: Joshua
 * Date: 10/27/2015
 * Time: 2:59 PM
 */

namespace TestingCenter\Utilities;

use TestingCenter\Http\Methods;

class Testing
{
    const JSON = 'application/json';
    const FORM = 'application/x-www-form-urlencoded';

    /**
     * @param $url Full URL to call
     * @param string Http Method to use. See constants in Http\Methods
     * @param string $body Appropriately formated body content
     * @param string $token A token if required
     * @param string $type JSON | FORM
     * @return mixed false on failure, string on success.
     */
    public static function callAPIOverHTTP($url, $method = Methods::GET, $body = "", $token = "", $type = self::JSON)
    {
        $headers = array();
        $token = trim($token,'"'); //remove quotes from token string
        array_push($headers, "Content-Length: " . strlen($body));
        array_push($headers, "Authorization: Bearer $token");
        array_push($headers, "Content-Type: $type");//application/json
        // 1. initialize
        $ch = curl_init();

        // 2. set the options, including the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // 3. execute and fetch the resulting HTML output
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}