#!/usr/bin/perl
use strict;
use Data::Dumper;
use HTML::TableExtract;

use Text::CSV;

my $csv = Text::CSV->new ( { binary => 1 } )  # should set binary attribute.
                 or die "Cannot use CSV: ".Text::CSV->error_diag ();


my $path = "/home/paul/Downloads/";
my $inFileNameSportCodes = "NCAA-SportsCodes.csv";
my $inFileNameSportInfo = "NCAA-SportsInfo-";
my $outFileName = "NCAA-SportsInfo.csv";


mainProcess($path, $inFileNameSportCodes, $inFileNameSportInfo, $outFileName);

sub mainProcess{
    my $path = shift @_;
    my $inNm = shift @_;
    my $inNmPartial = shift @_;
    my $outNm = shift @_;

    my $fullNm = $path . $inNm;
    open (my $inFH, "$fullNm") ||die "can't open $fullNm";

    my $fullNm = $path . $outNm;
    open (my $outFH, ">", "$fullNm") ||die "can't open $fullNm";

    while (<$inFH>){
        chomp;
        my($sportCode,$sportName) = split(/,/,$_);
        print "processing the sportCode:$sportCode\t$sportName\n";
        processFile($path, $sportCode, $inNmPartial, $outFH);
        print "one down\n";
    }
    close $inFH;
    close $outFH;


}

sub processFile{
    my $path = shift @_;
    my $sportCode = shift @_;
    my $inNmPartial = shift @_;
    my $outFH = shift @_;

    my $fullNm = $path . $inNmPartial . $sportCode . ".html";
    open (my $inFH, "$fullNm") ||die "can't open $fullNm";

    my $content = "";
    while (<$inFH>){
        $content .= $_;
    }
    close $inFH;

    $content =~ s/\n//g;
    $content =~ s/\t//g;

#    for ($iDepth = 1; $iDepth <= 1; $iDepth++) {
    for (my $iDepth = 1) {

#         for ($iCount = 0; $iCount <= 15; $iCount++) {
         for (my $iCount = 0) {

            my $te = ();
            $te = HTML::TableExtract->new( depth => $iDepth, count => $iCount );
            $te->parse( $content );

            foreach my $ts ( $te->tables() )
            {
                foreach my $row ( $ts->rows() ){
                    #print "depth:$iDepth  count:$iCount\t" .  join(", ", @$row) . "\n";

                    s/^\s+|\s+$//g for (@$row);   ## trim ALL the items
                    if (length($$row[3])){
                        ##print "this is reclass:$$row[3]\n";
                        $$row[2] = $$row[3];      ## update the real division with the RECLASS
                    }

                    splice @$row, 3, 1;           ## remove the RECLASS item

                    print $outFH "$sportCode,";
                    #print Dumper($row);
                    $csv->print ($outFH, $_) for $row ;
                    print $outFH "\n";
                #print $fhOUT "depth:$iDepth,count:$iCount," .  join(", ", @$row) . "\n";
                }
            }

         }

     }
     print "done process $sportCode\n";

}