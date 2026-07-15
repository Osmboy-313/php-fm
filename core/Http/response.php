<?php

function buildResponse($data, int $status = 200, string $key = "", bool $flatten = false){
    
    $response = [
        "success" => $status < 400,
    ];

    if(!isEmpty($key) && $flatten) $flatten = false;
    if(is_string($data) && $flatten) $flatten = false;

    if(isEmpty($key) && !$flatten){
        if(is_array($data)) $key = "data";
        else if(is_string($data)) $key = "message";
    }


    if($flatten && is_array($data)){
        foreach($data as $k => $v){
            $response[$k] = $v;
        }
    }else{
        $response[$key] = $data;
    }

    $response = [
        ...$response,
        "time_date" => date("g:i:s A - l, jS F Y") . " - timezone: " . date_default_timezone_get(),
        "status" => $status
    ];

    return $response;
}

function abort($data, int $status = 200, string $key = "", bool $flatten = false){
    $response = buildResponse($data, $status, $key, $flatten);
    throw new ApiException($response);
}

function sendResponse(array $response){
    if(isEmpty($response) || !isset($response["status"])) return;

    http_response_code($response["status"]);
    unset($response["status"]);
    
    // header("Content-Type: application/json");
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

?>