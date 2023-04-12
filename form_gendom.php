<?php

//form generate

$form_gen = new MyFormDom($htd->get_dom(), "form_gen", $_SERVER['REQUEST_URI']);


$htd->add_css_link('tooltips.css');

$form_gen->set_get_string('generate'); //this is for reset_all at first

$form_gen->add_text_element('h1', 'QComb v1.1');
$form_gen->add_text_element('h2', 'Combination generator');


$form_gen->add_text("Do the necessary: ");

//help try
$form_gen->add_entity_ref('ensp'); //bigger than nbsp; html4/5

$req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
setVar('allhelpurl', '"' . "$req_scheme://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=help" . '"');
$help_link = $form_gen->add_text_element('a', ' Help ');
$help_link->setAttribute('href', '');
$help_link->setAttribute('onclick', "helpOpen(allhelpurl); return false;"); //false - not to refresh parent
$help_link->setAttribute("style", "position:relative; left:155px; font-style:italic;font-weight:bold;font-size:125%;");

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

//$form_gen->add_br();
$form_gen->add_br();

//1.1

//from-to text
$chars_from_to = $form_gen->add_text_element("p", "Smalls - from:  to: ; CAPS - from:  to: ");

$form_gen->add_id_to_element($chars_from_to, "chars_from_to");

$form_gen->add_text("Need to exclude charachters: ");

$need_exclude_edit = $form_gen->add_input_element("text", "need_exclude_edit");
$need_exclude_edit->setAttribute("size", "95");

$form_gen->add_br();
$form_gen->add_br();

 
//\1.1

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

//1.1 dev
$form_gen->add_br();
$form_gen->add_br();

$form_gen->add_entity_ref('ensp'); //bigger than nbsp; html4/5
$form_gen->add_entity_ref('ensp');
$form_gen->add_entity_ref('ensp');

//trying to radio

//ordinary way (nothing to change)
$ordinary_radio = $form_gen->add_input_element("radio", "ordinary_radio");
$ordinary_radio->setAttribute("name", "ids_source");
$ordinary_radio->setAttribute("value", "ordinaryyes");

$ordinary_radio->setAttribute("checked", "");

//when retutn from prod id
$ordinary_radio->setAttribute("onchange", "sayStandartGenerate()");


$ordinary_label = $form_gen->add_sametype_element("label", "ordinary_label");

$ordinary_label->setAttribute("for", "ordinary_radio");

$form_gen->set_inner_text($ordinary_label, " ordinary way ");

//ordinary toolttip
$ordinary_label->setAttribute("class", "mytooltip");
$ordinary_ttip_txt = "Generation in a table which can be downloaded as a csv file.";
$ordinary_ttip =  $form_gen->add_text_element("span", $ordinary_ttip_txt);
$ordinary_ttip->setAttribute("class", "mytooltiptext");
$ordinary_label->appendChild($ordinary_ttip);
//o tttip

//maps id
$mapids_radio = $form_gen->add_input_element("radio", "mapids_radio");
$mapids_radio->setAttribute("name", "ids_source");
$mapids_radio->setAttribute("value", "mapidsyes");

//when retutn from prod id
$mapids_radio->setAttribute("onchange", "sayStandartGenerate()");


$mapids_label = $form_gen->add_sametype_element("label", "mapids_label");

$mapids_label->setAttribute("for", "mapids_radio");

$form_gen->set_inner_text($mapids_label, " map ids from csv ");

//mapsid toolttip
$mapids_label->setAttribute("class", "mytooltip");
$mapids_ttip_txt = "Map already known ids (ex. S/N) from csv file while generating combinations.";
$mapids_ttip =  $form_gen->add_text_element("span", $mapids_ttip_txt);
$mapids_ttip->setAttribute("class", "mytooltiptext");
$mapids_label->appendChild($mapids_ttip);
//m tttip


//produce from id
$prodids_radio = $form_gen->add_input_element("radio", "prodids_radio");
$prodids_radio->setAttribute("name", "ids_source");
$prodids_radio->setAttribute("value", "prodidsyes");
$prodids_radio->setAttribute("onchange", "sayProduceBuild(this)");

$prodids_label = $form_gen->add_sametype_element("label", "prodids_label");

$prodids_label->setAttribute("for", "prodids_radio");

$form_gen->set_inner_text($prodids_label, " produce from ids (csv) ");

//prodids toolttip
$prodids_label->setAttribute("class", "mytooltip");
$prodids_ttip_txt = "Generate the combinations from alredy known ids in one way (hashed) dependency (losing randomness, see Help).";
$prodids_ttip =  $form_gen->add_text_element("span", $prodids_ttip_txt);
$prodids_ttip->setAttribute("class", "mytooltiptext");
$prodids_label->appendChild($prodids_ttip);
//p tttip


//produce from single id
$singleid_radio = $form_gen->add_input_element("radio", "singleid_radio");
$singleid_radio->setAttribute("name", "ids_source");
$singleid_radio->setAttribute("value", "singleidyes");
$singleid_radio->setAttribute("onchange", "sayProduceBuild(this)");

$singleid_label = $form_gen->add_sametype_element("label", "singleid_label");

$singleid_label->setAttribute("for", "singleid_radio");

$form_gen->set_inner_text($singleid_label, " produce from single id (input) ");

//singleid toolttip
$singleid_label->setAttribute("class", "mytooltip");
$singleid_ttip_txt = "Same as the former case but for one combination,  id entered manually.";
$singleid_ttip =  $form_gen->add_text_element("span", $singleid_ttip_txt);
$singleid_ttip->setAttribute("class", "mytooltiptext");
$singleid_label->appendChild($singleid_ttip);
//s tttip


//setVar('idChboxes', '["mapids_chbox", "prodids_chbox"]');
setVar('idRadios', '["mapids_radio", "prodids_radio"]');
//1.1

$form_gen->add_br();
$form_gen->add_br();


$gen_submit = $form_gen->add_input_element("submit", "generate"); //needs onclick js func and a callable
$gen_submit->setAttribute("value", "Generate");
//it will open in the same tab (chrome wins)

//it comes at first of all!
$gen_submit->setAttribute("onclick", "openfile(this)"); //1.1

$form_gen->add_br();
$form_gen->add_br();


$hover_text = $form_gen->add_text_element("p", "(Hover over a radio button text to get more information.)");
$hover_text->setAttribute("style", "position:relative; left:140px; font-style:italic;font-weight:bold;");

$form_gen->add_br();



//end of visual elements

//1.1
//set settings from form to CG closure

$form_to_CG_call = function($form) {

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

    //1.1
    if (isset($_POST['ids_source']) && $_POST['ids_source'] == 'mapidsyes') $_SESSION['CG']->insist_id_map();
    else $_SESSION['CG']->insist_id_map(false);

    if (isset($_POST['ids_source']) && $_POST['ids_source'] == 'prodidsyes') $_SESSION['CG']->insist_id_produce(1);
    elseif (isset($_POST['ids_source']) && $_POST['ids_source'] == 'singleidyes') $_SESSION['CG']->insist_id_produce(2);
    else $_SESSION['CG']->insist_id_produce(0);
    //upper  if - attention (big)

    //need exclude
    if (strlen($_POST['need_exclude_edit'])) $_SESSION['CG']->chsbuilder->set_a_need('exclude', $_POST['need_exclude_edit']);
    else $_SESSION['CG']->chsbuilder->set_a_need('exclude', "");



}; //form_to_CG_call closure

$form_gen->add_helper_callable('helpformtoCG', $form_to_CG_call);
//\1.1

//goto charset callable part ->

$gotochset_call = function($form) {

    filelog("callable ch_set");

//1.1
//this function brings form controls info in CG
    $_SESSION['CG']->copy_reqs_to_previous();
    $form->helper_call('helpformtoCG');
//\1.1

    $_SESSION['CG']->set_comb_size($_POST['passnumbchar']);
    $_SESSION['CG']->set_comb_numb($_POST['passnumber']);

    filelog("in gotochs calla before JS redurect");

    $req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    //phpAlert($req_scheme);
    echo '<script type="text/javascript">window.location.replace("' . "$req_scheme://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=charset" .  '")</script>';

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


//1.1
//same as in gotochset_call
    try {
        $_SESSION['CG']->copy_reqs_to_previous();
        $form->helper_call('helpformtoCG');

        if ($_SESSION['CG']->reqs_differ_from_previous()) {
            $_SESSION['CG']->chsbuilder->build_from_needs(); //now they are set at first in constr of chsbuilder
        }
    
        $_SESSION['CG']->set_comb_size($_POST['passnumbchar']);
        $_SESSION['CG']->set_comb_numb($_POST['passnumber']);
    
    
        //conform part
        filelog("IN HELPER REQS ID PROBL: " . print_r($_SESSION['CG']->get_requirements(), true) ."\n"); //tuk kazva spec 1
        //filelog
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
    	$_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';
        throw new Exception('incorrect set of conditions: ' . $badmessage);
            }  
    
        filelog("IN GENERATE - BEFORE ECHOING TH WINDOW OPEN...show, ID PROD: " . $_SESSION['CG']->get_id_producing() . "\n");
    
    } //try 
    catch (Exception $e) {
        phpAlert($e->getMessage());
        filelog("IN GENERATE_CL - CATCH EXCEPTION, BEFORE RESET ALL CALL \n");
        $form->submit_call('resetall'); 
        return;
    } //catch

//\1.1

    $req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

    echo '<script type="text/javascript">window.open("' . "$req_scheme://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=showtable" .  '", "_self")</script>';

};

$form_gen->add_submit_callable('generate', $generate_call);


$resetall_call = function($form) {
    $form->reset_all(); //1.1
};

$form_gen->add_submit_callable('resetall', $resetall_call);




$form_gen_init = function($form) {

    $form->add_form_page_title("QComb v1.1 - Generate");

    $form->get_dom()->getElementByID("generate")->setAttribute("type", "submit"); //unconditional, I thing its ok...

    $_SESSION['CG']->set_charset(); //sets ascii95 or user defined one (CG is ready now)

    filelog("FGENINIT BEFORE CCHSMEMO REPL CHSET::" . $_SESSION['CG']->chsbuilder->sniff_charsetstr() . "\n");
    $form->replace_inner_text("charset_memo", $_SESSION['CG']->chsbuilder->sniff_charsetstr()); //the charset set by user


    //setting comb size and number
    filelog("CG - READY: " . $_SESSION['CG']->is_ready() . "\n");
    if (!$_SESSION['CG']->is_ready()) return;
    //(at the very first run CG is not ready and all controls are in default state, here init function exits)
    //(CG is populated to be ready in generate and gotochset submit callables) - former situation

    //now chsbuilder get its first needs in its constr and then makes build_from_needs() 

    

    $form->get_dom()->getElementByID("passnumbchar")->setAttribute("value", $_SESSION['CG']->get_comb_size());


    if (!($_SESSION['CG']->get_id_producing() || $_SESSION['CG']->get_id_mapping())) 
        $form->get_dom()->getElementByID("passnumber")->setAttribute("value", $_SESSION['CG']->get_comb_numb());
    else 
        $form->get_dom()->getElementByID("passnumber")->setAttribute("value", 10);

    //1.1
    filelog("FGENINIT BEFORE conform_reqs_toneeds, LAST_SUBMIT_NAME:" . $_SESSION['LAST_SUBMIT_NAME'] . "\n");
    if ($_SESSION['LAST_SUBMIT_NAME'] === 'previewchset') $_SESSION['CG']->conform_reqs_toneeds();
    //be careful, it means sendbtn
    //only this case - from chset send

    //\1.1

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

    //1.1
    //trying to radio
    if ($_SESSION['CG']->get_id_mapping())
        $form->set_attr_novalue("mapids_radio", "checked");
    else $form->get_dom()->getElementByID("mapids_radio")->removeAttribute("checked"); 



    if ($_SESSION['CG']->get_id_producing() === 1) { //id_prod multi
        $form->set_attr_novalue("prodids_radio", "checked");
        $form->set_attr_novalue("need_exclude_edit", "readonly");
    }
    else $form->get_dom()->getElementByID("prodids_radio")->removeAttribute("checked"); 

    if ($_SESSION['CG']->get_id_producing() === 2) { //think about it (single)
        $form->set_attr_novalue("singleid_radio", "checked");
        $form->set_attr_novalue("need_exclude_edit", "readonly");
    }
    else $form->get_dom()->getElementByID("singleid_radio")->removeAttribute("checked"); 
    //upper two ifs - attention


    //neeed to exclude
    $need_exclude = $_SESSION['CG']->chsbuilder->get_a_need('exclude');
    if (strlen($need_exclude))
        $form->get_dom()->getElementByID("need_exclude_edit")->setAttribute("value", $need_exclude);
    else
        $form->get_dom()->getElementByID("need_exclude_edit")->setAttribute("value", "");

    //from-to text
    $from = $_SESSION['CG']->chsbuilder->get_a_need('littles')[0];
    $to = $_SESSION['CG']->chsbuilder->get_a_need('littles')[-1];
    $FROM = $_SESSION['CG']->chsbuilder->get_a_need('CAPS')[0];
    $TO = $_SESSION['CG']->chsbuilder->get_a_need('CAPS')[-1];

    if (!$_SESSION['CG']->get_req_from_ch_sort('littles')) {
        $from = "";
        $to = "";
    }

    if (!$_SESSION['CG']->get_req_from_ch_sort('CAPS')) {
        $FROM = "";
        $TO = "";
    }

    //bold is not easy...


    $form->replace_inner_text("chars_from_to", "Specified needs: "); //initial
    
    
    $smalls_str_bold =  $form->add_text_element("b", "Smalls"); //the class name
    
    $form->append_child_to_parent("chars_from_to", $smalls_str_bold); //adding it
    
    $form->add_inner_text_by_id("chars_from_to", " - from: "); //adding intermediate text
    
    
    $from_bold =  $form->add_text_element("b", $from); //the from value, bold
    
    $form->append_child_to_parent("chars_from_to", $from_bold); //adding it
    
    $form->add_inner_text_by_id("chars_from_to", " to: "); //adding intermediate text
    
    
    $to_bold =  $form->add_text_element("b", $to); //the to value, bold
    
    $form->append_child_to_parent("chars_from_to", $to_bold); //adding it
    
    
    $form->add_inner_text_by_id("chars_from_to", "; "); //adding intermediate text
    
    
    $caps_str_bold =  $form->add_text_element("b", "CAPS"); //the class name
    
    $form->append_child_to_parent("chars_from_to", $caps_str_bold); //adding it
    
    
    $form->add_inner_text_by_id("chars_from_to", " - from: "); //adding intermediate text
    
    
    $FROM_bold =  $form->add_text_element("b", $FROM); //the FROM value, bold
    
    $form->append_child_to_parent("chars_from_to", $FROM_bold); //adding it
    
    $form->add_inner_text_by_id("chars_from_to", " to: "); //adding intermediate text
    
    
    $TO_bold =  $form->add_text_element("b", $TO); //the to value, bold
    
    $form->append_child_to_parent("chars_from_to", $TO_bold); //adding it
    
    
    $form->add_inner_text_by_id("chars_from_to", "."); //adding the final point
    

    //\the bold mess...

    filelog("FGEN_INIT -THE END, requirements: : " . print_r($_SESSION['CG']->get_requirements(), true) . "\n");
    filelog("FGEN_INIT -THE END, exclude need: " . $_SESSION['CG']->chsbuilder->get_a_need('exclude') . "\n");
    

}; //closure init


$form_gen->set_init_callable($form_gen_init);

?>
