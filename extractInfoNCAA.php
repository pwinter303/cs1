<?php

include 'simple_html_dom.php';



### FILES and URLS
$urlSportCodes = 'http://web1.ncaa.org/onlineDir/exec2/sponsorship';
$fNmSportCodesHTML = '/home/paul/Downloads/NCAA-SportsCodes.html';
$fNmSportCodesCSV = '/home/paul/Downloads/NCAA-SportsCodes.csv';
$fNmSportInfoCSV = '/home/paul/Downloads/NCAA-SportsInfo.csv';


########## ADJUST THESE AS NEEDED!  - EG - DONT RETRIEVE WEB DATA UNLESS NECESSARY
retrieveSportCodesFromWeb($urlSportCodes,$fNmSportCodesHTML);
generateSportCodeCSV($fNmSportCodesHTML, $fNmSportCodesCSV);

//retrieveSportInfoFromWeb($fNmSportCodesCSV);



// THIS FOLLOWING FUNCTION DOES NOT WORK.. USE THE PERL SCRIPT TO GET THE FINAL FILE!
//generateSportInfoCSV($fNmSportCodesCSV,$fNmSportInfoCSV);



############## START FUNCTIONS  #####################################

##################################### GET SPORTS CODE HTML #####
function retrieveSportCodesFromWeb($url,$fNm) {
    getURLWriteLocal($url,$fNm);
}


##################################### EXTRACT SPORTS CODES #####
function generateSportCodeCSV($inNm, $outNm){
        #### OPEN OUTPUT FILE
        $sportCodeFH = fopen($outNm, "w") or die("Unable to open file 1!");

        #### READ INPUT FILE
        print "going to read SportCode HTML from file:$inNm\n";
        $htmlNBR1 = file_get_html($inNm);

        foreach( $htmlNBR1->find('select[name="sport"] option') as $option ){
            $sportCode = $option->value;
            $sportName = $option->plaintext;
//            $txt = "$sportCode,$sportName\n";
            $cellData = array($sportCode,$sportName);
            fputcsv($sportCodeFH, $cellData);
//            fwrite($sportCodeFH, $txt);
        }
        fclose($sportCodeFH);
}

##################################### GET SPORTS INFO HTML #####
function retrieveSportInfoFromWeb($fileNM){
        $handle = fopen($fileNM,'r');
        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                $sportCode = $data[0];
                print "sportCode:$sportCode\n";
                $url = "http://web1.ncaa.org/onlineDir/exec2/sponsorship?sortOrder=0&division=All&sport=" . $sportCode;
                $localFileName = "/home/paul/Downloads/NCAA-SportsInfo-$sportCode.html";
                getURLWriteLocal($url,$localFileName);
        }
        fclose($handle);
}



#####################################  EXTRACT SPORTS INFO DRIVER #####
function generateSportInfoCSV($fileNM, $outNm){

        $sportInfoFH = fopen($outNm, "w") or die("Unable to open file 7!");

        $handle = fopen($fileNM,'r');
        while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                $sportCode = $data[0];
                print "sportCode:$sportCode\n";
                $localFileName = "/home/paul/Downloads/NCAA-SportsInfo-$sportCode.html";
                processSportCode($sportCode, $localFileName, $sportInfoFH);
        }
}



#####################################  EXTRACT SPORTS INFO #####
function processSportCode($sportCode, $inNm, $outFH) {

        print "reading this file to get sports info:$inNm\n";
        $html = file_get_html($inNm);
        $table = $html->find('table', 1);
        $rowData = array();


        $items = array_slice($html->find('tr'), 1);

        foreach($items as $item){
            print "THIS IS AN ITEM $item\n\n\n\n\n\n";
        }
        die;

        foreach($table->find('tr') as $row) {
            print "found row\n";
            $myRowAsString = $row;
//            print "myRowAsString:$myRowAsString\n\n\n\n\n";

            // initialize array to store the cell data from each row
            $cellData = array();

            foreach($row->find('td') as $cell) {
                    $myRowAsString = $cell;
//                    print "myRowAsString:$myRowAsString\n";
//                    print "this is plaintext:$cell->plaintext and this is inner:$cell->innertext\n";
                    //die;

                    //foreach($row->find('td') as $cell) {
                    // push the cell's text to the array
                    //print "found td as cell\n";
                    //print "this is plaintext:$cell->plaintext and this is inner:$cell->innertext\n";
                    $cellData[] = $cell->plaintext;
            }
//            echo "$cellData[0]--\t$cellData[1]--\t$cellData[2]--\t$cellData[3]--\t$cellData[4]--\t$cellData[5]\n";
            $txt = "$sportCode\t";
            fwrite($outFH, $txt);
            fputcsv($outFH, $cellData);

        }
}




#####################################  GET WEB PAGE AND WRITE TO LOCAL DISK #####
function getURLWriteLocal($url,$localNM) {
        print "Getting data from this URL:$url\n";
        print "HTML will be written to:$localNM\n";

        $html = file_get_contents($url);
        ###############echo "$html";
        $FH = fopen($localNM, "w") or die("Unable to open file to hold raw HTML!");
        fwrite($FH, $html);
        fclose($FH);
}



?>