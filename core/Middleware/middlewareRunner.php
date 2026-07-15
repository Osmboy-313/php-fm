<?php

function runMiddleware(array $middlewares, string $projectRoot){
    if(isEmpty($middlewares)) return;

    foreach($middlewares as $middleware => $stuff){
        
        requireFile($middleware, $projectRoot);
        requireFile((array) $stuff["dependencies"], $projectRoot);

        foreach($stuff["functions"] as $func){
            if(isEmpty($func)) continue;

            if(!function_exists($func["func"])){
                return buildResponse("Middleware Function doesn't exist: '" . $func["func"] . "'", 404);
            }

            $args = $func["args"];
            $response = [];
            if(!isEmpty($args)) $response = $func["func"]($args);
            else $response = $func["func"]();
            if(!isEmpty($response)) return $response;
        }
    }
}


function runMiddleware11(array $middlewares, string $projectRoot){
    if(isEmpty($middlewares)) return;

    foreach($middlewares as $middleware => $stuff){
        
        requireFile($middleware, $projectRoot);
        requireFile((array) $stuff["dependencies"], $projectRoot);

        foreach($stuff["functions"] as $func){
            if(isEmpty($func)) continue;

            if(!function_exists($func["func"])){
                return buildResponse("Middleware Function doesn't exist: '" . $func["func"] . "'", 404);
            }
            
            $args = $func["args"];
            $response = [];
            if(!isEmpty($args)) $response = $func["func"]($args);
            else $response = $func["func"]();
            if(!isEmpty($response)) return $response;
        }
    }
}


?>