<?php


function resource_rest_router(array $routes, array $middlewareRegistry, string $projectRoot){

    $parsedRequest = parseRequest(array_keys($routes));
    if(!isSuccess($parsedRequest)) sendResponse($parsedRequest);


    $validatedRequest = validateRequest($routes, $parsedRequest);
    if(!isSuccess($validatedRequest)) sendResponse($validatedRequest);

    $resource = $validatedRequest["resource"];
    if(isEmpty($resource)) sendResponse(buildResponse("Resource not found!", 404));

    $middlewares = $routes[$resource]["middleware"] ?? "";

    // Note: I can use try catch method if I use abort() function otherwise the success checks work! So there is flexibility.

    // Well Im using try catch as well, let's say the user uses the abort() functiosn then there must be somethinhg to catch that, so catchng that right now, and also supporting the isset and false checks inside the try, now all good! 

    try{
        $middlewareResponse = runMiddleware($middlewares["before"], $middlewareRegistry, $projectRoot);
        if(!isSuccess($middlewareResponse)) sendResponse($middlewareResponse);
    
    }catch(Exception $e){
        sendResponse((array) $e);
    }

    requireFile([
            $routes[$resource]["controller"] ?? "",
            $routes[$resource]["service"] ?? "",
            $routes[$resource]["repository"] ?? "",
        ],
        $projectRoot
    );

    $handler = $routes[$resource]["handler"] ?? "";
    if(isEmpty($handler) || !function_exists($handler)) sendResponse(buildResponse('Handler not found!', 404));
    
    $rules = $routes[$resource]["rules"] ?? "";
    $controllerResponse = [];

    // Both checks cuz the user might be doing anything "return BuildResponse()" or using abort(), so....

    try{
        $controllerResponse = $handler($projectRoot, $parsedRequest["method"], $validatedRequest["id"], $validatedRequest["action"], $parsedRequest["queryParams"], $rules);
        if(!isSuccess($controllerResponse)) sendResponse($controllerResponse);
        if(isEmpty($controllerResponse)) $controllerResponse = [];

    }catch(Exception $e){
        sendResponse((array) $e);
    }

    if(isset($middlewares["after"]) && !isEmpty($middlewares["after"])){
        $afterMiddlewareResponse = runMiddleware($middlewares["after"], $middlewareRegistry, $projectRoot, $controllerResponse);
        sendResponse($afterMiddlewareResponse);
    }

    sendResponse($controllerResponse);
}

function parseRequest($allowedRoutes){

    $pos = stripos($_SERVER["REQUEST_URI"], "api/");
    if($pos === false) return buildResponse("Invalid Api Endpoint", 404);
    
    $url = trim(substr($_SERVER["REQUEST_URI"], $pos), "/");
    $method = $_SERVER["REQUEST_METHOD"];

    $queryParams = [];
    $queryParamsRaw = stripos($url, "?") !== false ? trim(substr($url, stripos($url, "?"))) : "";
    $queryParamsRaw = stripos($url, "?") !== false ? explode("&", str_replace("?", "", $queryParamsRaw)) : [];

    if(!isEmpty($queryParamsRaw)){
        foreach($queryParamsRaw as $key => $queryParamRaw){
            $parts = explode("=", $queryParamRaw);
            if(isEmpty($parts[0])) continue;
            $queryParams[$parts[0]] = $parts[1] ?? "";
        }
    }

    $url = stripos($url, "?") !== false ? str_ireplace(substr($url, stripos($url, "?")), "", $url) : $url;
    $segments = preg_split("/\//", $url);
    $segments = array_map(fn($v) => trim(strtolower($v)), $segments);
    
    if($segments[0] !== "api") return buildResponse("Invalid Api Endpoint!", 404);
    if(isEmpty($segments[1] ?? "")) return buildResponse("Invalid Resource!", 404);
    if(!in_array($segments[1], $allowedRoutes)) return buildResponse("Invalid Resource: '" . ucfirst($segments[1])."'" , 404);


    if(!isEmpty($segments[4] ?? "")){
        $error = "you passed 5 arguments, only 4 arguments are supported along with query parameters, the pattern should look like this:
        GET    /api/resource                  → all
        GET    /api/resource/{id}             → one
        POST   /api/resource                  → create
        PUT    /api/resource/{id}             → update
        DELETE /api/resource/{id}             → delete
        POST   /api/resource/{id}/{action}    → action on single record
        POST   /api/resource/{action}         → action on all records";
        return buildResponse($error, 414);
    }

    return ["method" => $method, "resource" => $segments[1], "others" => [$segments[2] ?? "", $segments[3] ?? ""], "queryParams" => $queryParams];
}

function validateRequest(array $routes, array $parts){

    if(!isset($routes[$parts["resource"]])) return buildResponse("Invalid Resource!", 404);

    $resource = $parts["resource"];
    $segments = $parts["others"];
    $id = $action = "";

    $singleActions = array_map(fn($v) => strtolower($v), array_keys($routes[$resource]["actions"]["single"] ?? []));
    $bulkActions = array_map(fn($v) => strtolower($v), array_keys($routes[$resource]["actions"]["bulk"] ?? []));


    if(isEmpty($segments[1] ?? "") && in_array($segments[0], $bulkActions)){
        $action = $segments[0];
        $action = !isEmpty($action) ? [$action => $routes[$resource]["actions"]["bulk"][$action]] : [];
    }else{
        $id = $segments[0] ?? "";
        $action = $segments[1] ?? "";

        if(!isEmpty($id) && (in_array($id, $singleActions) || in_array($id, $bulkActions))){
            return buildResponse("Id shouldn't be any kind of action!", 404);   
        }

        if(!isEmpty($action) && !in_array($action, $singleActions)){
            return buildResponse("Invalid action: '{$action}'!", 404);
        }

        $action = !isEmpty($action) ? [$action => $routes[$resource]["actions"]["single"][$action]] : [];
    }

    return ["resource" => $resource, "id" => $id, "action" => $action];
}


?>