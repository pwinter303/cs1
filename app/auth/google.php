<?php
#echo "hi<br>";
#echo $_SERVER['HTTP_REFERER'];

// var_dump(file_get_contents('php://input'));

 $request = json_decode(file_get_contents('php://input'));
 #var_dump($request);
 // echo $request->code;
 // echo "\n";
 // echo $request->clientId;


#var_dump($params);

$url = 'https://accounts.google.com/o/oauth2/token';


$params = [
    'code' => $request->code,
    'client_id' => $request->clientId,
    'client_secret' => '',
    'redirect_uri' => $request->redirectUri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$accessTokenResponse = curl_exec( $ch );

// $accessToken = json_decode($accessTokenResponse->getBody(), true);

$decodedResponse = json_decode($accessTokenResponse);
echo "accessTokenResponse:\n"; 
var_dump($decodedResponse);


echo "this is access token: " . $decodedResponse->access_token . "\n";

// $profileResponse = $client->request('GET', '', [
//     'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
// ]);

$url = 'https://www.googleapis.com/plus/v1/people/me/openIdConnect';
$url = 'https://www.googleapis.com/auth/plus.profile.emails.read';

// $params = [
//     'headers' => ['Authorization ' => 'Bearer ' .  "$decodedResponse->access_token"]
// ];

$params = [
    'headers' => array('Authorization' => 'Bearer ' . "$decodedResponse->access_token")
];


echo "params:\n";
var_dump($params);

$ch1 = curl_init( $url );
curl_setopt( $ch1, CURLOPT_HTTPGET, 1);
curl_setopt( $ch1, CURLOPT_POSTFIELDS, 0);
curl_setopt( $ch1, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch1, CURLOPT_HEADER, 1);
curl_setopt( $ch1, CURLOPT_RETURNTRANSFER, 1);
curl_setopt( $ch1, CURLOPT_HTTPHEADER, $params);

$profileResponse = curl_exec( $ch1 );
echo "this is the profile Response:\n";
var_dump($profileResponse);
$decodedProfileResponse = json_decode($profileResponse);

echo "decodedProfileResponse:\n";
var_dump($decodedProfileResponse);



$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=> array('Authorization' => 'Bearer ' . "$decodedResponse->access_token")
));

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents($url, false, $context);

var_dump($file);






?>
