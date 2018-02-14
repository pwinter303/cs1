<?php

require '../../vendor/autoload.php';

use \GuzzleHttp\guzzle;
use \Firebase\JWT\JWT;

require '../../config/config.php';
//
$request = json_decode(file_get_contents('php://input'));

$googleSecret = GOOGLESECRET;

$params = [
    'code' => $request->code,
    'client_id' => $request->clientId,
    'client_secret' => $googleSecret,
    'redirect_uri' => $request->redirectUri,
    'grant_type' => 'authorization_code'
];

print "this is the access code:$request->code\n";

$client = new GuzzleHttp\Client();

// Step 1. Exchange authorization code for access token.
$response = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
    'form_params' => $params
]);
$accessTokenResponse = json_decode($response->getBody(), true);

//THIS IS WHAT IS STORED IN THE $accessTokenResponse
// from: https://developers.google.com/identity/protocols/OpenIDConnect
//access_token	A token that can be sent to a Google API.
//id_token	A JWT that contains identity information about the user that is digitally signed by Google.
//expires_in	The remaining lifetime of the access token.
//token_type	Identifies the type of token returned. At this time, this field always has the value Bearer.
//refresh_token (optional)	This field is only present if access_type=offline is included in the authentication request. For details, see Refresh tokens.


echo "this is access token response: \n";
var_dump($accessTokenResponse);

$accessToken = $accessTokenResponse['access_token'];
$idToken = $accessTokenResponse['id_token'];
$expiresIn = $accessTokenResponse['expires_in'];
$tokenType = $accessTokenResponse['token_type'];

echo "this is the idToken which is the JWT:$idToken\n";
echo "this is the googleSecret:$googleSecret\n";

//
//$gSecretDecode = base64_decode($googleSecret);
//
//$decoded = JWT::decode( $idToken, base64_decode(strtr($googleSecret, '-_', '+/')) );
//var_dump($decoded);


//THIS WORKS... in that it decodes the JWT from Google which has the eMail... BUT it doesnt do the straight decode using the secret key
$tks = explode('.', $idToken);
list($headb64, $bodyb64, $cryptob64) = $tks;

$header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64));
$body = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
var_dump($header);
print "done dumping header";
var_dump($body);
print "done dumping body";
var_dump($cryptob64);
print "done dumping Crypto";

////This didnt work... got JWT errors
//// Get public keys from URL as an array
//$publicKeyURL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
//$key = json_decode(file_get_contents($publicKeyURL), true);
//var_dump($key);

//$decoded = JWT::decode($idToken, $key, array('RS256'));

////$key = "example_key";
//$token = array(
//    "iss" => "http://example.org",
//    "aud" => "http://example.com",
//    "iat" => 1356999524,
//    "nbf" => 1357000000
//);
//
///**
// * IMPORTANT:
// * You must specify supported algorithms for your application. See
// * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
// * for a list of spec-compliant algorithms.
// */
//$jwt = JWT::encode($token, $googleSecret);
//$decoded = JWT::decode($jwt, $googleSecret, array('HS256'));
//print_r($decoded);

// from: https://stackoverflow.com/questions/15104682/how-to-get-user-email-with-google-access-token
$url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $accessToken;
$profileResponse = $client->request('GET',$url);
$profile = json_decode($profileResponse->getBody(), true);
echo "this is profile:\n ";
var_dump($profile);

?>
