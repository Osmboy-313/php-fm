<?php

function performAction(string $projectRoot, array $action, string $method, string $table, array $data, array $rules, int|string $id){
    if(isEmpty($action) || !is_array($action)){
        return buildResponse("Invalid Action passed! it must be in the proper supported structure", 404);
    }

    try{

        $actionKey = array_key_first($action);
        $actionDetails = $action[$actionKey];

        if(($actionDetails[0] ?? "") !== $method) return buildResponse("Invalid Request for action: '{$actionKey}'", 405);

        $mainFile = $actionDetails[1][0] ?? "";
        $func = $actionDetails[1][1] ?? "";
        $dep = !isEmpty($actionDetails[2] ?? "") ? (array) $actionDetails[2] : [];
        
        if(isEmpty($mainFile)) return buildResponse("No File/Class Provided For Action!", 404);

        $result = requireFile([$mainFile, ...$dep], $projectRoot);
        if(!isSuccess($result)) return $result;
        if(!function_exists($func)) return buildResponse("Handler for action: '{$actionKey}' doesn't exist!", 404);

        $result = $func($table, $data, $rules, $id);
        return $result;
    } catch(Exception $e){
        // log($e->getMessage()); ->> for future logging!
        return buildResponse("Dispatching Actions Failed: ", 500);
    }
}

?>