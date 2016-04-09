<?php
session_start();

//include 'db.php';
//include 'functions.php';

//PLW Added 2016-04-06
date_default_timezone_set('America/New_York');

//if(isset($_SESSION['authenticated'])){
    $result = processRequest();
//} else {
//    ### must be logged in to use this...
//    header('HTTP/1.1 401 Unauthorized');
//    $response{'StatusCd'} = 401;
//    return $response;
//}
$json = json_encode($result);
echo $json;
########################################################
function processRequest(){
//  if (isset($_SESSION['customer_id'])) {
//    $customer_id = $_SESSION['customer_id'];
//  }   else  {
//     die ('invalid customer id');
//  }
    $customer_id = 0;  // temp hack
    switch ($_SERVER['REQUEST_METHOD']) {
       case 'POST':
             $result = processPost($customer_id);
             break;
       case 'GET':
             $result = processGet($customer_id);
             break;
       default:
             echo "Error:Invalid Request";
             break;
    }
    return $result;
}
####################  GETs ################################
function  processGet($customer_id){
//    $dbh = createDatabaseConnection();
    $action = htmlspecialchars($_GET["action"]);
    switch ($action) {
       case 'getColleges':
             $result = getColleges();
             break;
       case 'getDirections':
             $result = getDirections();
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }
    return $result;
}
####################  POSTs ################################
function  processPost($customer_id){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
//    $request = convertFromBoolean($request);
//    $dbh = createDatabaseConnection();
    $dbh = 'FIXME';
    $action = $request->action;
    switch ($action) {
       case 'saveCollege':
             $result = saveCollege($dbh, $request, $customer_id);
             break;
       case 'saveLocation':
             $result = saveLocation($dbh, $request, $customer_id);
             break;
       case 'getDirections':
             $result = getDirectionsAsPOST($dbh, $request);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }
    return $result;
}


function  getColleges(){
    $colleges = array( array( name => "Georgetown University",
                          location => "3700 O St NW, Washington, DC"
                        ),
                   array( name => "Princeton University",
                          location => "One Nassau Hall, Princeton, NJ"
                        ),
                   array( name => "Miss Porters",
                          location => "Farmington,CT"
                        ),
                   array( name => "Pennsylvania State University",
                          location => "University Park, PA"
                        ),
                   array( name => "University of Pittsburgh",
                          location => "Pittsburgh,PA"
                        ),
                   array( name => "Ohio State University",
                          location => "Columbus,OH"
                        ),
                   array( name => "UMASS Amherst",
                          location => "Amherst, MA"
                        ),
                   array( name => "UMASS Boston",
                          location => "Boston, MA"
                        ),
                   array( name => "UVM",
                          location => "Burlington, VT"
                        ),
                   array( name => "Dartmouth",
                          location => "Hanover, NH"
                        ),
                   array( name => "Brown",
                          location => "Providence,RI"
                        )
                 );

    return $colleges;
//    array(['name' => "COLLEGE1"],
//                 ['name' => "COLLEGE2"],
//                 ['name' => "COLLEGE3"]);
}

function  getDirections(){
//    return 1;
    $passedData = ($_GET);

    $passedDataDecoded = json_decode($passedData{'routePoints'});
//    echo ("from" => $from);
//    var_dump($passedData{'routePoints'});
//    var_dump($passedDataDecoded);
    var_dump($passedDataDecoded{'name'});

}

function  getDirectionsAsPOST($dbh, $request_data){
    $junk = $request_data->routePoints->start->name;
    var_dump($junk);
}
function  saveCollege(){
    return 1;
}
function  saveLocation(){
    return 1;
}

?>