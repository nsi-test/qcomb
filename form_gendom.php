<?php

require_once "htmldocdom.php";

$form_gen = new MyFormDom($htd->get_dom(), "form_gen", $_SERVER['REQUEST_URI']);

$form_gen->add_text_element('h1', 'QComb v1.0');
$form_gen->add_text_element('h2', 'Combination generator');


$form_gen->add_text("Do the necessary: ");
$form_gen->add_br();
$form_gen->add_br();

$form_gen->add_text("Current character set: ");
$form_gen->add_br();
$charset_memo = $form_gen->add_sametype_element("textarea", "charset_memo");
$charset_memo->setAttribute("cols", "40");
$charset_memo->setAttribute("rows", "5");
$form_gen->set_attr_novalue("charset_memo", "readonly");
$form_gen->add_br();

$form_gen->add_text("Refine the character set to use: ");

$gotochset_submit = $form_gen->add_input_element("submit", "gotochset"); //needs a callable
$gotochset_submit->setAttribute("value", "Character set...");


$form_gen->add_entity_ref('ensp'); //bigger than nbsp; html4/5
$form_gen->add_entity_ref('ensp');
$form_gen->add_entity_ref('ensp');
$gotochset_submit = $form_gen->add_input_element("submit", "resetall"); //a callable...
$gotochset_submit->setAttribute("value", "Reset all...");


$form_gen->add_br();
$form_gen->add_br();

$form_gen->add_text("Complexity requirements: ");

$glittles_chbox = $form_gen->add_input_element("checkbox", "glittles_chbox");
$glittles_chbox->setAttribute("value", "littlesok");
$form_gen->set_attr_novalue("glittles_chbox", "checked");
//checked by default
$form_gen->add_text(" small letters; ");

$gcaps_chbox = $form_gen->add_input_element("checkbox", "gcaps_chbox");
$gcaps_chbox->setAttribute("value", "capsok");
$form_gen->set_attr_novalue("gcaps_chbox", "checked");
//checked by default
$form_gen->add_text(" capital letters; ");

$gdigits_chbox = $form_gen->add_input_element("checkbox", "gdigits_chbox");
$gdigits_chbox->setAttribute("value", "digitsok");
$form_gen->set_attr_novalue("gdigits_chbox", "checked");
//checked by default
$form_gen->add_text(" digits; ");

$gspecials_chbox = $form_gen->add_input_element("checkbox", "gspecials_chbox");
$gspecials_chbox->setAttribute("value", "specialsok");
$form_gen->set_attr_novalue("gspecials_chbox", "checked");
//checked by default
$form_gen->add_text(" special characters; ");

$gspace_chbox = $form_gen->add_input_element("checkbox", "gspace_chbox");
$gspace_chbox->setAttribute("value", "spaceok");
//not checked by default
$form_gen->add_text(" space (not recomended); ");

$form_gen->add_br();
$form_gen->add_br();

//uniqueness
$form_gen->add_text("Uniqueness requirements: ");

$guniques_chbox = $form_gen->add_input_element("checkbox", "guniques_chbox");
$guniques_chbox->setAttribute("value", "uniquesok");
$form_gen->set_attr_novalue("guniques_chbox", "checked");
//checked by default
$form_gen->add_text(" yes (recomended) ");

$form_gen->add_br();
$form_gen->add_br();

$form_gen->add_text("Enter the number of password characters: ");

$passnumbchar = $form_gen->add_input_element("text", "passnumbchar"); //needs onblur js func
$passnumbchar->setAttribute("size", "2");
$passnumbchar->setAttribute("value", "8");
$passnumbchar->setAttribute("onblur", "generateEdits.every(validateInt)");

$form_gen->add_text(" Enter the number of passwords to generate: ");

$passnumber = $form_gen->add_input_element("text", "passnumber");
$passnumber->setAttribute("size", "5");
$passnumber->setAttribute("value", "10");
$passnumber->setAttribute("onblur", "generateEdits.every(validateInt)");

$form_gen->add_br();
$form_gen->add_br();

setVar('generateEdits', '["passnumbchar", "passnumber"]');

setVar('notshowa', 'false'); //because of chrome onblur-alert() loop (see myalert(message) in js funcs)

$form_gen->add_text("Generate your passwords: ");

$form_gen->add_br();
$form_gen->add_br();


$gen_submit = $form_gen->add_input_element("submit", "generate"); //needs onclick js func and a callable
$gen_submit->setAttribute("value", "Generate");
//it will open in the same tab (chrome wins)

//it comes at first of all!


$form_gen->add_br();

//end of visual elements


//goto charset callable part ->

$gotochset_call = function($form) {

    filelog("callable ch_set");

    if (isset($_POST['glittles_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('littles');
    else $_SESSION['CG']->set_req_from_ch_sort('littles', false);

    if (isset($_POST['gcaps_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('CAPS');
    else $_SESSION['CG']->set_req_from_ch_sort('CAPS', false);

    if (isset($_POST['gdigits_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('digits');
    else $_SESSION['CG']->set_req_from_ch_sort('digits', false);

    if (isset($_POST['gspecials_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('specials');
    else $_SESSION['CG']->set_req_from_ch_sort('specials', false);

    if (isset($_POST['gspace_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('space');
    else $_SESSION['CG']->set_req_from_ch_sort('space', false);

    //uniqueness
    if (isset($_POST['guniques_chbox'])) $_SESSION['CG']->insist_uniques();
    else $_SESSION['CG']->insist_uniques(false);

    $_SESSION['CG']->set_comb_size($_POST['passnumbchar']);
    $_SESSION['CG']->set_comb_numb($_POST['passnumber']);

    filelog("in gotochs calla before JS redurect");

    echo '<script type="text/javascript">window.location.replace("' . "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=charset" .  '")</script>';

    filelog("in gotochs calla - BEFOORE ABS END}");

};

$form_gen->add_submit_callable('gotochset', $gotochset_call);


//generate callable part ->
$generate_call = function($form) {

    if (!filter_var($_POST['passnumbchar'], FILTER_VALIDATE_INT)) {
        phpAlert('not correct: password characters number must be integer, ' . $_POST['passnumbchar'] . ' - given');
        return;
    }
        
    if (!filter_var($_POST['passnumber'], FILTER_VALIDATE_INT)) {
        phpAlert('not correct: password number must be integer, ' . $_POST['passnumber'] . ' - given');
        return;
    }

    if (isset($_POST['glittles_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('littles');
    else $_SESSION['CG']->set_req_from_ch_sort('littles', false);

    if (isset($_POST['gcaps_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('CAPS');
    else $_SESSION['CG']->set_req_from_ch_sort('CAPS', false);

    if (isset($_POST['gdigits_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('digits');
    else $_SESSION['CG']->set_req_from_ch_sort('digits', false);

    if (isset($_POST['gspecials_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('specials');
    else $_SESSION['CG']->set_req_from_ch_sort('specials', false);

    if (isset($_POST['gspace_chbox'])) $_SESSION['CG']->set_req_from_ch_sort('space');
    else $_SESSION['CG']->set_req_from_ch_sort('space', false);

    //uniqueness
    if (isset($_POST['guniques_chbox'])) $_SESSION['CG']->insist_uniques();
    else $_SESSION['CG']->insist_uniques(false);

    $_SESSION['CG']->set_comb_size($_POST['passnumbchar']);
    $_SESSION['CG']->set_comb_numb($_POST['passnumber']);

    //conform part
    $_SESSION['CG']->conform_chset_toreqs();
    //it has to be called separated


//works but needs attention
    
    $correct = $_SESSION['CG']->check_correctness();
    if ($correct !== true) {
        $maxmin = $_SESSION['CG']->get_max_min();
	$real_possible_combnumb = gmp_add(gmp_sub($maxmin['combMaxNum'], $maxmin['combMinNum']), gmp_init(1));
	$user_required_combnumb = strval($_SESSION['CG']->get_comb_numb());

        if ($correct === -1) $badmessage = "uniqueness and possible set of combinations smaller than required set! ($real_possible_combnumb / $user_required_combnumb)";

        if ($correct === -2) $badmessage = "requred combination size smaller than requred number of sorts of symbols! (" . strval($_SESSION['CG']->get_comb_size() . " / " . $_SESSION['CG']->get_reqsort_number()) . ")";


        filelog("ALERT< CORRECTNESS - WRONG!!! \n" . $badmessage . "\n");
        phpAlert('incorrect set of conditions: ' . $badmessage);
	$_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';
        return;
        }
     


    echo '<script type="text/javascript">window.open("' . "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=showtable" .  '", "_self")</script>';

};

$form_gen->add_submit_callable('generate', $generate_call);


$resetall_call = function($form) {

    reset_it();

};

$form_gen->add_submit_callable('resetall', $resetall_call);




$form_gen_init = function($form) {
    $_SESSION['CG']->set_charset(); //sets ascii95 or user defined one
    $form->set_inner_text($form->get_dom()->getElementByID("charset_memo"), $_SESSION['CG']->chsbuilder->sniff_charsetstr()); //the charset set by user

    //setting comb size and number
    if (!$_SESSION['CG']->is_ready()) return;
    //at the very first run CG is not ready and all controls are in default state, here init function exits
    //CG is populated to be ready in generate and gotochset submit callables 

    $form->get_dom()->getElementByID("passnumbchar")->setAttribute("value", $_SESSION['CG']->get_comb_size());

    $form->get_dom()->getElementByID("passnumber")->setAttribute("value", $_SESSION['CG']->get_comb_numb());

    //setting req chboxes
    if (!$_SESSION['CG']->check_reqs_regularity()) return; //space or nothing is wrong

    if ($_SESSION['CG']->get_req_from_ch_sort('littles'))
        $form->set_attr_novalue("glittles_chbox", "checked");
    else $form->get_dom()->getElementByID("glittles_chbox")->removeAttribute("checked"); 
    
    if ($_SESSION['CG']->get_req_from_ch_sort('CAPS'))
        $form->set_attr_novalue("gcaps_chbox", "checked");
    else $form->get_dom()->getElementByID("gcaps_chbox")->removeAttribute("checked"); 

    if ($_SESSION['CG']->get_req_from_ch_sort('digits'))
        $form->set_attr_novalue("gdigits_chbox", "checked");
    else $form->get_dom()->getElementByID("gdigits_chbox")->removeAttribute("checked"); 

    if ($_SESSION['CG']->get_req_from_ch_sort('specials'))
        $form->set_attr_novalue("gspecials_chbox", "checked");
    else $form->get_dom()->getElementByID("gspecials_chbox")->removeAttribute("checked"); 

    if ($_SESSION['CG']->get_req_from_ch_sort('space'))
        $form->set_attr_novalue("gspace_chbox", "checked");
    else $form->get_dom()->getElementByID("gspace_chbox")->removeAttribute("checked"); 

    //uniqueness
    if ($_SESSION['CG']->get_uniqueness())
        $form->set_attr_novalue("guniques_chbox", "checked");
    else $form->get_dom()->getElementByID("guniques_chbox")->removeAttribute("checked"); 

    filelog("IN FGEN INIT END REQS: ". print_r($_SESSION['CG']->get_requirements(), true));
};


$form_gen->set_init_callable($form_gen_init);

?>





















