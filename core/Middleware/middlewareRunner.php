<?php


function runMiddleware(array $middlewares, array $middlewareRegistry, string $projectRoot, $controllerResponse = ""){
    if(isEmpty($middlewares) || isEmpty($middlewareRegistry)) return;

    foreach($middlewares as $k => $v){    
        if(!in_array($k, array_keys($middlewareRegistry))) return buildResponse("Middleware with key '{$k}' not found", 404);

        $mainFile = $middlewareRegistry[$k][0] ?? "";
        if(isEmpty($mainFile)) return buildResponse("No File provided for Middleware: '{$k}'", 404);

        $dependencies = !isEmpty($middlewareRegistry[$k][1] ?? "") ? (array) $middlewareRegistry[$k][1] : [];
        $result = requireFile([$middlewareRegistry[$k][0], ...$dependencies], $projectRoot);
        if(!isSuccess($result)) return $result;

        foreach($v as $handler){
            if(isEmpty($handler)) continue;

            [$func, $args] = $handler;

            if(!function_exists($func)){
                return buildResponse("Middleware Function doesn't exist: '" . $handler["func"] . "'", 404);
            }

            $args = !isEmpty($args) ? (array) $args : [];
            $response;

            if(!isEmpty($args)) $response = $func(...$args);
            else $response = $func();

            if(!isEmpty($response)) return $response;
        }
    }
}

?>