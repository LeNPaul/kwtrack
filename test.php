<?php
//$curl = curl_init();
//
//$authCode = 'ANZfCokTxAHxrLekPHlx';
//$url = 'https://api.amazon.com/auth/o2/token';
//$data = 'grant_type=authorization_code&code=' . $authCode . '&redirect_uri=https://ppcology.io/&client_id=amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf&client_secret=9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3';
//
//$options = array(
//  'Content-Type:application/x-www-form-urlencoded;charset=UTF-8'
//);
//
//curl_setopt($curl, CURLOPT_URL, $url);
//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLOPT_HTTPHEADER, $options);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//
//$result = curl_exec($curl);
//
//curl_close($curl);
//
//var_dump($result);


$jsonString = '{"access_token":"Atza|IwEBIKxtEXnG4hH-Cctsb33jxGJ3vtbvc_qZHjIzKI2QEUXbFK3c0muJT6cazJOJDhSo8eVZE3TYlv5ycdTCaa8yfN31--cCcQzQCHFdyvMYGK5CkX_0CqHCMMfxSZoRYvRCYGVMTecFkH_n1tYqrFIMKBcwNr8xQmghsJM_YQhrCbDL7CVfYBbCQWTq36m0OOQHVHDfiRB9U3d6PEYPEMl9Gg3SQAWAQBJdnXOT4gvYluvkL2DJy9bRgiT3P3D1khvwYgOD-V6dWR5E-YAnWOG4Ly3f9llFq45voizCKE69mxtA_qh-8JzIAIJZRd2jqF4PTdPsdgpst-lxw_1Pe0hGLqfEa4Bf0h6j9BMJH-GhdsAv-At3WyHbpAOmdBhlJ25goJAdd80NxE7gOWzVVlPU887IiX3pWp93Tfs6ZSKUAyZeLrgdkpjRTf8uGgtelmgjxvPD5yOaaor6skvMHt5aqYSItnf4S_mIb5QJeM9RXrrT-tKjyQeLJxYmYqIazKxNcfJ3fL7g0WPWl5EwzOKo4h9G-Cv3ZiOFIR8g6MOasPumtfnwWIdCwnISua_grN6NgasIt-q53K4CKR_tgu4BCvwY","refresh_token":"Atzr|IwEBIIOq09cP-83QthYHdyVams8vS9yTnVVsTOu4O5xZe7jJ1WaBa1_7n1N4kPqksQsWFlIH4Jb3wqDG8TaZ4o_nxGy0zXk30YxaUIJ00povC8ZmWVCl4v4RAWqwtmjcQnHhpgWinHBYXhflD5kBI4KCg_R8bU_-eRmuPCzVDbF-DqKMubUBkC-pxlzwyS-LoGBot1zcm_bHHyfrqQhcUecK1lY1xeCC2Ch_bkNSHfG2Ae0_6kjNoL2d3QSQi7-WUE09UM6EixTMEPUdN82QYWq1j6X5KD6WIWkl6Hw3SDyLpWj_KhcY1m6ivXa0kkV1-FlDaEfGHrGwtAqygkPmxVatSptnxr58tnJY5jGwgT_klOpmdmOAElneTG4SgZOr-VCs0iOm3QgX6ZHrLgcj6EUb7mLIhUXsyInmM4HeMxundAJ9oNnyT4v7KDR-1qrwUi1d031bRRSY57SCalq2Drw3T0FfSX3ia9CNQZEHCxMmOV5QqDErzsZApbkP5IL0LhvNxi8Jt7veu_mOynCF1bjzV7MfdUE-u38s_pueVY31Zt2-0itEJuNa5iu9mfKgKjs5dOP5uZZZd2GNhfi2icX-kV5M","token_type":"bearer","expires_in":3600}';
$result = json_decode(stripslashes($jsonString),true);

echo '<pre>';
var_dump($result);
echo '</pre>';

echo $result['refresh_token'];