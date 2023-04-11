<?php


require_once "htmldocdom.php";

$form_show = new MyFormDom($htd->get_dom(), "form_show", $_SERVER['REQUEST_URI']);

$form_show->add_text_element('h2', 'Your combinations: ');


$form_show->add_br();
$back_hlink = $form_show->add_sametype_element("a", "back_hlink");
$back_hlink->setAttribute("href", "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=generate");
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

$form_show->add_br();
$form_show->add_br();

$combinations_table = $form_show->add_sametype_element("table", "combinations");

$form_show->format_table_by_cols_array("combinations", array("###", "Combination"));


try {
    if ($_SESSION['CG']->is_ready() && $_SESSION['LAST_SUBMIT_NAME'] === 'generate') {
        filelog("last submit name: " . $_SESSION['LAST_SUBMIT_NAME']);
	$form_show->populate_table_from_array("combinations", $_SESSION['CG']->get_combinations(), true);
    }

    else $_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';
}
catch (Exception $e) {

phpAlert("Error caught: " . $e->getMessage());
}



$form_show_init = function($form) {
    if (!$_SESSION['CG']->is_ready()) {
        phpAlert("Combination generator is not set up");
	exit();
    }

}; //closures end with ';'

$form_show->set_init_callable($form_show_init);



?>
