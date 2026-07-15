<?php

function generateJwt(array $payload, string $secret, int $expiry = 3600){

    $header = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];

    $payload = [
        ...$payload,
        "iss" => $_SERVER["HTTP_HOST"] ?? "localhost",
        "aud" => $_SERVER["HTTP_ClIENT"] ?? "localhost",
        "nbf" => "",
        "iat" => time(),
        "exp" => time() + $expiry,
        "jti" => bin2hex(random_bytes(32)),
    ];

    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));

    $signature = hash_hmac("SHA256", "$headerEncoded.$payloadEncoded", $secret, true);

    $signatureEncoded = base64UrlEncode($signature);

    return "$headerEncoded.$payloadEncoded.$signatureEncoded";
}

function verifyJwt(string $jwt, string $secret){
    $parts = explode(".", $jwt);
  
    if(count($parts) !== 3){
        return buildResponse(['valid' => false, 'message' => 'Invalid Token Format'], 400);
    }

    [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

    $signature = hash_hmac("SHA256", "$headerEncoded.$payloadEncoded", $secret, true);
    $expectedSignature = base64UrlEncode($signature);

    if(!hash_equals($expectedSignature, $signatureEncoded)){
        return buildResponse(['valid' => false, 'message' => 'Invalid Signature, seems to be fabricated!'], 400);
    }

    $header = json_decode(base64UrlDecode($headerEncoded), true);
    $payload = json_decode(base64UrlDecode($payloadEncoded), true);

    if (isset($payload['exp']) && $payload['exp'] <= time()) {
        return buildResponse(['valid' => false, 'message' => 'Token expired'], 400);
    }

    if (isset($payload['iat']) && $payload['iat'] > time()) {
        return buildResponse(['valid' => false, 'message' => 'Token issued in the future'], 400);
    }
    
    return buildResponse(['valid' => true, "payload" => $payload], 200, "", true);
}


function extractPayload(string $jwt, string $secret){

    $verification = verifyJwt($jwt, $secret);
    if(isset($verification["valid"]) && !$verification["valid"]){
        return $verification;
    }

    return $verification["payload"];
}


function setTokenCookie($name, $token, $expiry){

    setcookie($name, $token, [
        'expires'  => time() + $expiry,
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

}

function implementTokens(array $payload, int $accessTokenExpiry = 900, int $refreshTokenExpiry = 604800, string $oldRefreshToken = ""){

    $jwtSecret = getenv("JWT_SECRET");
    $refreshSecret = getenv("JWT_REFRESH_SECRET");

    $accessToken = generateJwt($payload, $jwtSecret, $accessTokenExpiry);
    $refreshToken = generateJwt($payload, $refreshSecret, $refreshTokenExpiry);

    // This SaveRefreshToken Function is a function in JwtService, that first deletes the existing ones, if they exist otherwise just create a new one! so each one we delete and make a new one, also known as rotation ;)

    // Changing it btw right now!

    // $result = saveRefreshToken($payload["sub"], $refreshToken, $refreshTokenExpiry, $oldRefreshToken);
    // if(isset($result["success"]) && !$result["success"]) return $result;

    // setTokenCookie("ACCESS_TOKEN", $accessToken, $accessTokenExpiry);
    // setTokenCookie("REFRESH_TOKEN", $refreshToken, $refreshTokenExpiry);
}

function removeTokenCookies(){
    setcookie("ACCESS_TOKEN", "", time() - 3600, "/");
    setcookie("REFRESH_TOKEN", "", time() - 3600, "/");
}



// ==== The Other Jwt Functions like that handle the refresh rotations are in the service of an app, not in the framework itself, but Im thinking about adding interfaces here so whoever uses, will make those functions and add their own logic, like sacing in redis or cache instead of DB, so no hardcoding or rigidity! ===


?>