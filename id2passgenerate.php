<?php

require_once "htmldocdom.php";

$ini_conf = "id2passgenerate.ini";

filelog("first message from script...");

$frontcombstr = "";
//use $argc for argument count...

$usage = <<<EOT
Usage:
php $argv[0] <input id csv file> [<--frontstring> <"some string">]

EOT;

if (!in_array($argc, [2, 4])) exit("wrong, try --help\n");


if ($argv[1] === "--help") exit($usage);

if ($argc === 4  &&  $argv[2] === "--frontstring") {

    $frontcombstr = $argv[3];

}

//read from ini:

$ini_array = parse_ini_file($ini_conf, true);

print_r($ini_array);


$outfilename = $ini_array['local']['outfilename'];

if (!file_exists($argv[1])) exit("no such file: $argv[1]\n");

require "combgenerator_smooth_gmp.php";

$cg = new CombGenerator_GMP();

if ($ini_array['combgenerator']['uniqueness'])
    $cg->insist_uniques(); //default false

$cg->set_charset(); //ansi95



//requirments from string bit mask
$cg->set_reqs_from_mask($ini_array['combgenerator']['reqs_mask']);

//letter range part (small, caps)
$smalls_range = $ini_array['combgenerator']['smalls_range'];
$caps_range = $ini_array['combgenerator']['caps_range'];

if ($smalls_range || $caps_range) $cg->chsbuilder->reset_charsetstr();

if (strlen($smalls_range) == 2) {
    foreach (str_split($smalls_range) as $sc) {
        if (!in_array($sc, $cg->chsbuilder->give_littles())) throw new Exception("wrong smalls range character!");
    }
    $cg->chsbuilder->get_ordinary($smalls_range[0], $smalls_range[-1]);
}


if (strlen($caps_range) == 2) { 
    foreach (str_split($caps_range) as $cc) {
        if (!in_array($cc, $cg->chsbuilder->give_CAPS())) throw new Exception("wrong caps range character!");
    }
    $cg->chsbuilder->get_ordinary($caps_range[0], $caps_range[-1]);
}

if ($smalls_range || $caps_range) $cg->set_charset(); //here a new wcomb object is set

echo "DEEBUG IN id2, CHSTR after range part : " . $cg->chsbuilder->sniff_charsetstr() . "\n";


//(needs conform_chset_toreqs() below)
$cg->conform_chset_toreqs();

//exclude part
if ($ini_array['combgenerator']['to_exclude']) {
    $cg->chsbuilder->set_excluded($ini_array['combgenerator']['to_exclude']);
    $cg->chsbuilder->exclude_them();
    
}

if ($ini_array['combgenerator']['shuffle_charset']) {
    $cg->chsbuilder->shuffle_charset();
    echo "character set was shuffled!\n";	
}

$cg->set_charset(); //updated charset

echo "charset to apply:" . $cg->get_charset() . "\n\n";


#read csv part


$csvsrc = fopen($argv[1], "r");

$csv_ar = array(); //2 dimensional after that

while (!feof($csvsrc)) {array_push($csv_ar, fgetcsv($csvsrc, 0, ";"));}

//print_r($csv_ar);

fclose($csvsrc);
#\read

array_pop($csv_ar); //necessary, not very sure why

$cg->set_comb_size($ini_array['combgenerator']['passnumbchar']);

$cg->set_comb_numb(count($csv_ar));

if (!$cg->check_correctness()) {
    echo "incorrect set of conditions!\n\n";
    exit();
}

$combs = $cg->get_combinations();

$combtable = array(); //array of rows (arrays)


foreach ($combs as $index=>$comb) { 
    array_push($combtable, array(strval(++$index), $csv_ar[--$index][0], $frontcombstr . $comb));
}

array_unshift( $combtable, array('#', 'ID', 'password'));
//print_r($combtable);

$csvf = fopen($outfilename, "w");


foreach ($combtable as $combrow) {
    fwrite($csvf, implode($combrow, "\t") . "\n"); //only way for me...
}

fclose($csvf);




?>






