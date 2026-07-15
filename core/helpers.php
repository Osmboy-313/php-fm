<?php 

// It searches the index of an associative array and returns it with the structure, let say we have an array:
// $arr = [
//     "single" => [
//         "validate" => [],
//     ],
//     "bulk" => [
//         "isUnique" => [],
//     ],
// ];
// If I want to search for the key "validate" in the $actions array then it will return the array with that same strucutre


function search_r(array $array, ...$rest){

    $arr = [];
    
    foreach($array as $key => $value){
        if(in_array($key, $rest, true)){
            $arr[$key] = $value;
        }

        if(is_array($value)){
            $result = search_r($value, ...$rest);
            if(!isEmpty($result)){
                $arr[$key] = $result;
            }
        }
    }

    return $arr;
}

function base64UrlEncode(string $data){
    return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
}

function base64UrlDecode($data) {
    $padding = strlen($data) % 4;
    if ($padding) {
        $data .= str_repeat('=', 4 - $padding);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function isEmpty($subject, bool $strict = false){
    
    if($subject === "" || $subject === null || (is_array($subject) && count($subject) === 0)){
        return true;
    }

    if($strict && ($subject === false || $subject === 0 || $subject === "0")){
        return true;
    }

    return false;
}
