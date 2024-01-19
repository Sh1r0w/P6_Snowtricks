<?php

namespace App\Services;

use DateTimeImmutable;

class JWTService
{
     /**
      * The function generates a JSON Web Token (JWT) using the provided header, payload, secret, and
      * optional validity period.
      * 
      * @param array header An array containing the header information for the JWT. This typically
      * includes the algorithm used for signing the token (e.g., "HS256") and the type of token (e.g.,
      * "JWT").
      * @param array payload The payload is an array that contains the data you want to include in the
      * JWT (JSON Web Token). It can include any key-value pairs that you want to include in the token.
      * The function will encode this payload as JSON and include it in the JWT.
      * @param string secret The "secret" parameter is a string that represents the secret key used for
      * signing the JSON Web Token (JWT). It is used in the hash_hmac function to generate the
      * signature. The secret key should be kept confidential and should only be known by the server
      * generating the JWT and the server verifying the
      * @param int validity The validity parameter represents the duration of time for which the
      * generated JWT (JSON Web Token) will be considered valid. It is specified in seconds. By
      * default, the validity is set to 10800 seconds, which is equivalent to 3 hours.
      * 
      * @return string a JSON Web Token (JWT) as a string.
      */
     public function generate(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800
     ): string {
        if ($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        $base64Header = str_replace(['+', '/', '='],['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['','/','='],['-','_',''], $base64Signature);

        $jwt = $base64Header . '.' . $base64Payload .'.'. $base64Signature;

        return $jwt;

     }

     /**
      * The function checks if a given token is valid by matching it against a regular expression
      * pattern.
      * 
      * @param string token The token parameter is a string that represents a token.
      * 
      * @return bool a boolean value indicating whether the given token is valid or not.
      */
     public function isValid(string $token): bool
     {
        return preg_match('/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
        $token
    ) === 1;
     }

     /**
      * The function takes a token as input, splits it into three parts, decodes the second part from
      * base64 and returns it as an array.
      * 
      * @param string token A string representing a JSON Web Token (JWT).
      * 
      * @return array an array containing the decoded payload from the given token.
      */
     public function getPayload(string $token): array
     {
        $array = explode('.', $token);

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
     }

     /**
      * The function `getHeader` takes a token as input, splits it into an array, decodes the first
      * element of the array from base64 and returns the decoded header as an associative array.
      * 
      * @param string token A string representing a token.
      * 
      * @return array an array containing the decoded header information from the given token.
      */
     public function getHeader(string $token): array
     {
        $array = explode('.', $token);
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
     }

     /**
      * The function checks if a given token has expired by comparing its expiration timestamp with the
      * current timestamp.
      * 
      * @param string token A string representing a token.
      * 
      * @return bool a boolean value, indicating whether the token is expired or not.
      */
     public function isExpired(string $token): bool
     {
        $payload = $this->getPayload($token);
        
        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
     }

     /**
      * The check function verifies if a given token matches the generated token using the provided
      * secret.
      * 
      * @param string token The token is a string that represents a JSON Web Token (JWT). It typically
      * consists of three parts: a header, a payload, and a signature. The header and payload are
      * base64-encoded JSON strings, while the signature is used to verify the integrity of the token.
      * @param string secret The "secret" parameter is a string that represents a secret key or
      * password. It is used to generate a verification token and compare it with the provided token to
      * check if they match.
      * 
      * @return a boolean value. It will return true if the given token matches the generated
      * verification token, and false otherwise.
      */
     public function check(string $token, string $secret): bool
     {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
     }
}
