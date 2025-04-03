<?php

define("QC_VERSION", "v1.2.1");

require_once "htmldocdom.php";

require_once "myhtmldom.php";

$htd = new MyHtmlDom();


require_once "combgenerator_smooth_gmp.php";

filelog("IN maindom3 - BEFORE session_start()...");
session_start(); //it has to be after require of combgenerator, because otherwise it doesn't know what to do with the object in session
filelog("IN maindom3 - AFTER session_start()...");
filelog("IN maindom3 - the very beginning _POST: " . print_r($_POST, true));

if (!isset($_SESSION['CG'])) $_SESSION['CG'] = new CombGenerator_GMP();

if (!isset($_SESSION['LAST_SUBMIT_NAME'])) $_SESSION['LAST_SUBMIT_NAME'] = 'NONE';

//1.1
if (!isset($_SESSION['CSV_DATA'])) $_SESSION['CSV_DATA'] = [];

filelog("IN maindom3 - BEFORE req once form_gendom.php...");
require_once "form_gendom.php";
filelog("IN maindom3 - BEFORE req once form_chsetdom.php...");
require_once "form_chsetdom.php";
filelog("IN maindom3 - BEFORE req once form_showdom.php...");
require_once "form_showdom.php";
//upper lines have to be in that order (not talking about filelog())

filelog("IN maindom3 - BEFORE req once form_helpdom.php...");
require_once "form_helpdom.php"; //the desperate help...


filelog("\n\n\n");

if (!array_key_exists('form', $_GET)) {
    $htd->get_body()->appendChild($htd->get_dom()->createElement('p', 'NO FORM, EXITING.'));
    echo $htd->display();
    exit();
}




error_log("messache from qcomb"); //this function sends to error log of apache
filelog("maindom3 - before switch-case forms, going to case: " . $_GET['form']); //directory where the log file is, should be writable for apache user


$htd->add_script_src("jquery-3.6.0.js");

$htd->add_script_src("jquery-ui.js");

$htd->add_css_link("jquery-ui.css");

$htd->add_script_src("combgenhtml.js");



switch ($_GET['form']) {

    case "generate":

        $htd->replace_or_add_form($form_gen->get_form());

        if (isset($_SESSION['CG'])) {
	    $form_gen->init_call();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $form_gen->post_set($_POST);

        }

        echo $htd->display();

        break;

    case "charset":

        $htd->replace_or_add_form($form_chset->get_form());

        if (isset($_SESSION['CG'])) {
            $form_chset->init_call();         
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $form_chset->post_set($_POST);
        }

        echo $htd->display(); //here

        break;

    case "showtable":
    
	$htd->add_CSS('table, th, td { border: 1px solid black; }');
        $htd->replace_or_add_form($form_show->get_form());

	if (isset($_SESSION['CG'])) {
	    $form_show->init_call();
	}	
	 
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $form_show->post_set($_POST);
        }


        echo $htd->display();

        break;

    //1.1
    case "ajaxpost":

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            print_r($_POST);
         
            filelog("1.1 DEBUG  AJAX post: " . print_r($_POST, true) . "\n");
 
                if (isset($_POST['csvdata'])) {
                    //convert csv to arr
                    $csvdata = explode("\n", $_POST['csvdata']); 

                    array_pop($csvdata);
                  
                    filelog("1.1 DEBUG scvdata: " . print_r($csvdata, true) . "\n");
                    $_SESSION['CSV_DATA'] = [];
    
                    foreach ($csvdata as $row) array_push($_SESSION['CSV_DATA'], str_getcsv($row, ";"));
                    //id2passgenerate style
                    filelog("1.1 DEBUG session: " . print_r($_SESSION['CSV_DATA'], true) . "\n");

                }  
        }

    break;
        
    case "ajaxpostprodinfo":

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            print_r($_POST);
         
            filelog("1.1 DEBUG  AJAX2 post: " . print_r($_POST, true) . "\n");
 
                if (isset($_POST['ajaxmsg'])) {
                    
                    filelog("1.1 ajax2 prodidinfo set");
                    //wh??                   
                    $_SESSION['CG']->chsbuilder->build_for_id_produce();

                    if ($_POST['ajaxmsg'] == 'prodids_radio') $_SESSION['CG']->insist_id_produce(1); 

                    if ($_POST['ajaxmsg'] == 'singleid_radio') $_SESSION['CG']->insist_id_produce(2); 

                    //?$form_gen->init_call();

                    filelog("ajaxprodinfo, produce_id setting :" . $_SESSION['CG']->get_id_producing() . "\n");

                    filelog("end of ajaxpostprodinfo, exclude need: " . $_SESSION['CG']->chsbuilder->get_a_need('exclude') . "\n");


                }  
        }

    break;

    case "help":

        $htd->replace_or_add_form($form_help->get_form());

	if (isset($_SESSION['CG'])) {
	    $form_help->init_call();
	} //to be the same

        echo $htd->display(); 

    break;

    default:
        exit();


}





?>
