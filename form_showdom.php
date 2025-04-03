<?php


//form showtable


$form_show = new MyFormDom($htd, "form_show", $_SERVER['REQUEST_URI']); //8.x compat

$combs_heading = $form_show->add_text_element('h2', 'Your combinations: ');
$form_show->add_id_to_element($combs_heading, "combs_heading");

$form_show->add_br();
$back_hlink = $form_show->add_sametype_element("a", "back_hlink");

$req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$back_hlink->setAttribute("href", "$req_scheme://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=generate");
$form_show->set_inner_text($back_hlink, "Go back..."); 

$form_show->add_br();
$form_show->add_br();



//submit like hyperlink:
$again_submit = $form_show->add_input_element("submit", "generate"); //without a callable, sets LAST_SUBMIT_NAME in post_set, see below
$again_submit->setAttribute("value", "Do it again...");
$again_submit->setAttribute("style", "background:none; border-width:0px; color:blue; text-decoration:underline;cursor:pointer;");

$form_show->add_br();
$form_show->add_br();

$csv = $form_show->add_sametype_element("a", "csv"); //download hyperlink
$csv->setAttribute("href", "");
$csv->setAttribute("onclick", "downloadcsv()");
$csv->setAttribute("download", "combinations.csv");
$form_show->set_inner_text($csv, "download as csv (tab delimited)"); 


$form_show->add_entity_ref('ensp'); //bigger than nbsp; html4/5
//no setVar...
$help_link = $form_show->add_text_element('a', ' Help ');
$help_link->setAttribute('href', '');
$help_link->setAttribute('onclick', "helpOpen(allhelpurl); return false;");
$help_link->setAttribute("style", "position:relative; left:155px; font-style:italic;font-weight:bold;font-size:125%;");

$form_show->add_br();
$form_show->add_br();

$combinations_table = $form_show->add_sametype_element("table", "combinations");


$put_content_in_table = function($form) {  //index 1

    try {
        if ($_SESSION['CG']->is_ready() && $_SESSION['LAST_SUBMIT_NAME'] === 'generate') {

            filelog("IN SHOW_DOM TRY - last submit name: " . $_SESSION['LAST_SUBMIT_NAME']);

    
            //1.1
            filelog("INSHOW_DOMTRY INSIST_PRODICE?: " . $_SESSION['CG']->get_id_producing() . "\n");
            filelog("INSHOW_DOMTRY INSIST_MAPPING?: " . $_SESSION['CG']->get_id_mapping() . "\n");

            //id produce if...
            if ($_SESSION['CG']->get_id_producing()) {

                if (!$_SESSION['CSV_DATA']) {phpAlert("empty id list in produce!"); 
                filelog("IN HSOW TRY _ NO CSV_DATA\n");
                return;
                }
    

                $form->format_table_by_cols_array("combinations", array("###", "ID",  "Combination")); //1.1
                
                filelog("SHOW TRY before our pop " . $_SESSION['CG']->get_id_producing() . "\n");
        	$form->populate_table_from_array("combinations", $_SESSION['CG']->get_combinations($_SESSION['CSV_DATA']), true);
                return;
            } //if id producing...

    
            //id mapping if...
            if ($_SESSION['CG']->get_id_mapping()) {

                if (!$_SESSION['CSV_DATA']) {phpAlert("empty id list in mapping!"); //may be better... 
                filelog("IN HSOW TRY _ NO CSV_DATA\n");
                return;
                }

                $form->format_table_by_cols_array("combinations", array("###", "mappedID",  "Combination")); //1.1
    
                $combs2map = $_SESSION['CG']->get_combinations();
        
                $mapped_combinations = [];
    
                foreach ($combs2map as $index=>$mcomb) array_push($mapped_combinations, [$_SESSION['CSV_DATA'][$index][0], $mcomb[0]]); //two 2-dim arrays
                
                filelog("MAPPPED COMb: " . print_r($mapped_combinations, true) . "\n");

                $form->populate_table_from_array("combinations", $mapped_combinations, true);
     
                return;

            } //if id_mapping

            //\1.1


        //start of regular part, after ifs (return)
           
        $form->format_table_by_cols_array("combinations", array("###", "Combination")); //1.1

        filelog("SHOW TRY before old pop " . $_SESSION['CG']->get_id_producing() . "\n");
        $form->populate_table_from_array("combinations", $_SESSION['CG']->get_combinations(), true);
        filelog("SHOW TRY after old pop " . $_SESSION['CG']->get_id_producing() . "\n");
       
        } //is_ready && generate
    
        else $_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';
    }
    catch (Exception $e) {
    
    phpAlert("Error caught: " . $e->getMessage());
    }

}; //put_... init closure



$form_show_init = function($form) {  //index 0
    if (!$_SESSION['CG']->is_ready()) {
        phpAlert("Combination generator is not set up");
	exit();
    }

    //1.1

    $form->add_form_page_title("QComb " . QC_VERSION . " - Result");


    if ($_SESSION['CG']->get_id_producing()) {

        filelog("INSHOW GET_PRD INIT: " . $_SESSION['CG']->get_id_producing() . "\n");

        $form->replace_inner_text("combs_heading", "Your Id produced combinations: ");

        $_SESSION['CG']->chsbuilder->build_for_id_produce(); //it sets produce_case

        filelog("IN SHIOW INIT IF PRDC PRDCASE: " . $_SESSION['CG']->chsbuilder->is_produce_case() . "\n");
               
        $_SESSION['CG']->set_comb_size(8); //hard

        $_SESSION['CG']->set_comb_numb(count($_SESSION['CSV_DATA']));

        $_SESSION['CG']->chsbuilder->build_from_needs(); //?

        $_SESSION['CG']->set_charset();

        return;

    }


    if ($_SESSION['CG']->get_id_mapping()) {

        filelog("INSHOW GET_MAP INIT: " . $_SESSION['CG']->get_id_mapping() . "\n");

        $form->replace_inner_text("combs_heading", "Your Id mapped combinations: ");

        $_SESSION['CG']->set_comb_numb(count($_SESSION['CSV_DATA']));

        $_SESSION['CG']->chsbuilder->not_a_produce_case();

        return;

    }


    $_SESSION['CSV_DATA'] = [];
    $_SESSION['CG']->chsbuilder->not_a_produce_case();
    filelog("CG CHSB PRDC CASE: " . $_SESSION['CG']->chsbuilder->is_produce_case() . "\n");

    //\1.1

}; //closures end with ';'

$form_show->set_init_callable($form_show_init); //index 0

$form_show->set_init_callable($put_content_in_table); //index 1

$form_show->remove_me_from_dom(); //8.x compat

?>
