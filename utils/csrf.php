<?php

function createToken(int $bytes = 67, int $base = 16){
    $token = "";
    if($base === 16) $token = bin2hex(random_bytes($bytes));
    else if($base === 64) $token = base64UrlEncode(random_bytes($bytes));

    return $token;
}

function saveCSRF(string $token, bool $replace = false){
    
    if($replace) $_SESSION["CSRF_TOKEN"] = $token;
    else if(!isset($_SESSION["CSRF_TOKEN"])) $_SESSION["CSRF_TOKEN"] = $token;

}

function getCSRF(){
    return $_SESSION["CSRF_TOKEN"] ?? "";
}

function removeCSRF(){
    unset($_SESSION["CSRF_TOKEN"]);
}




?>