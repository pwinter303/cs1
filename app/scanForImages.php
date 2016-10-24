<?php

// Set content-type
header('content-type: application/json; charset=utf-8');

//original code
// Get the url
//$url = array_key_exists('url', $_POST)
//        ? $_POST['url']
//        : null;

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$url = $request->url;

// Create image finder
include('image_finder.class.php');
$finder = new ImageFinder($url);


// Get images
$images = $finder->get_images();


// Output result
$result = array('images' => $images);
//ob_start('ob_gzhandler');
echo json_encode($result);
