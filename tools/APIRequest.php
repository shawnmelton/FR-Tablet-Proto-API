<?php
class APIRequest {
    public static function createQS($params) {
        $qs = '';
        foreach($params as $param => $value) {
            $qs .= '&'. $param .'='. urlencode($value);
        }

        return $qs;
    }

    public static function sendPost($action, $params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . $action .'?status=for+rent&api_key='. API_KEY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        return curl_exec($ch);
    }

    public static function send($action, $params) {
        $ch = curl_init(API_URL . $action .'?status=for+rent' . self::createQS($params) . '&api_key='. API_KEY);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        return curl_exec($ch);
    }
}