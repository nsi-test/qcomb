<?php


require_once "htmldocdom.php";

$form_chset = new MyFormDom($htd->get_dom(), "form_chset", $_SERVER['REQUEST_URI']);

$form_chset->add_text_element('h2', 'Specify the character set:');

$form_chset->add_text("Choose: ");
$form_chset->add_br();
$form_chset->add_br();

//space choice part...
$space_chbox = $form_chset->add_input_element("checkbox", "space_chbox");
$space_chbox->setAttribute("value", "spaceok");
//not checked by default
$space_chbox->setAttribute("onchange", "disableSend(this)"); //send not available when changed
$form_chset->add_text(" include space (not recomended)");

$form_chset->add_br();
$form_chset->add_br();


//littles choice part...
$littles_chbox = $form_chset->add_input_element("checkbox", "littles_chbox");
$littles_chbox->setAttribute("value", "littleseok");
$form_chset->set_attr_novalue("littles_chbox", "checked");
//checked by default
$littles_chbox->setAttribute("onclick", "chbox_sel_checked(this)");
$littles_chbox->setAttribute("onchange", "disableSend(this)"); //send not available when changed
$form_chset->add_text(" include small letters - from ");

$ls = $_SESSION['CG']->chsbuilder->give_littles(); 

$littles_first_select = $form_chset->add_sametype_element("select", "l_first_sel");
$form_chset->add_children_from_array("l_first_sel", "option", $ls);
$littles_first_select->setAttribute("onchange", "disableSend(this);checkOrder(this);"); //send not available when changed

$form_chset->add_text(" to ");

$littles_last_select = $form_chset->add_sametype_element("select", "l_last_sel");
$form_chset->add_children_from_array("l_last_sel", "option", $ls);
$form_chset->set_child_attr_by_chindex("l_last_sel", "option", 25, "selected", "selected");
//25 - z - 26 from 0
$littles_last_select->setAttribute("onchange", "disableSend(this);checkOrder(this);"); //send not available when changed

$form_chset->add_br();
$form_chset->add_br();

//CAPS choice part...
$caps_chbox = $form_chset->add_input_element("checkbox", "caps_chbox");
$caps_chbox->setAttribute("value", "capsok");
$form_chset->set_attr_novalue("caps_chbox", "checked");
//checked by default
$caps_chbox->setAttribute("onclick", "chbox_sel_checked(this)");
$caps_chbox->setAttribute("onchange", "disableSend(this)"); //send not available when changed
$form_chset->add_text(" include CAPITAL letters - from ");

$cs = $_SESSION['CG']->chsbuilder->give_CAPS(); 

$caps_first_select = $form_chset->add_sametype_element("select", "c_first_sel");
$form_chset->add_children_from_array("c_first_sel", "option", $cs);
$caps_first_select->setAttribute("onchange", "disableSend(this);checkOrder(this);"); //send not available when changed

$form_chset->add_text(" to ");

$caps_last_select = $form_chset->add_sametype_element("select", "c_last_sel");
$form_chset->add_children_from_array("c_last_sel", "option", $cs);
$form_chset->set_child_attr_by_chindex("c_last_sel", "option", 25, "selected", "selected");
//25 - Z - 26 from 0
$caps_last_select->setAttribute("onchange", "disableSend(this);checkOrder(this);"); //send not available when changed

$form_chset->add_br();
$form_chset->add_br();


//digits choice part...
$digits_chbox = $form_chset->add_input_element("checkbox", "digits_chbox");
$digits_chbox->setAttribute("value", "digitsok");
$form_chset->set_attr_novalue("digits_chbox", "checked");
//checked by default
$digits_chbox->setAttribute("onchange", "disableSend(this)"); //send not available when changed
$form_chset->add_text(" include digits ");

$form_chset->add_entity_ref('ensp'); //bigger than nbsp; html4/5

//specials choice part...
$specials_chbox = $form_chset->add_input_element("checkbox", "specials_chbox");
$specials_chbox->setAttribute("value", "specialsok");
$form_chset->set_attr_novalue("specials_chbox", "checked");
//checked by default
$specials_chbox->setAttribute("onchange", "disableSend(this)"); //send not available when changed
$form_chset->add_text(" include special characters ");

$form_chset->add_br();
$form_chset->add_br();

//excluded choice part...
$form_chset->add_text("exclude: ");
$excluded_edit = $form_chset->add_input_element("text", "excluded_edit");
$excluded_edit->setAttribute("size", "40");
$excluded_edit->setAttribute("oninput", "disableSend(this)"); //send not available when input (oninput)

$form_chset->add_br();
$form_chset->add_br();


setVar('charsetInputs', '["space_chbox", "littles_chbox", "l_first_sel", "l_last_sel", "caps_chbox", "c_first_sel", "c_last_sel", "digits_chbox", "specials_chbox", "excluded_edit"]');

setVar('chboxes_selects', '{"littles_chbox": ["l_first_sel", "l_last_sel"], "caps_chbox": ["c_first_sel", "c_last_sel"]}');

//preview submit...
$form_chset->add_entity_ref('ensp');

$preview_submit = $form_chset->add_input_element("submit", "previewchset"); //needs a callable
$preview_submit->setAttribute("value", "Preview it");

$form_chset->add_br();
$form_chset->add_br();

//preview memo...
$form_chset->add_text("Preview of your choice: ");
$ccharset_memo = $form_chset->add_sametype_element("textarea", "ccharset_memo");
$ccharset_memo->setAttribute("cols", "60");
$ccharset_memo->setAttribute("rows", "10");
$form_chset->set_attr_novalue("ccharset_memo", "readonly");

//shuffle submit
$shuffle_submit = $form_chset->add_input_element("submit", "shufflechset"); //needs a callable...
$shuffle_submit->setAttribute("value", "Shuffle the character set...");

$form_chset->add_br();
$form_chset->add_br();

//send button
$form_chset->add_entity_ref('ensp');
$send_button = $form_chset->add_input_element("button", "sendbtn");
$send_button->setAttribute("value", "Send");
$send_button->setAttribute("onclick", "window.location.href='" . "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=generate" .  "'");


//callables..
//preview_callable...
$preview_call = function($form) {


            if ($_SESSION['CG']->chsbuilder->sniff_charsetstr()) $_SESSION['CG']->chsbuilder->reset_charsetstr();

            //building charset in chsbuilder & setting needs
            try {
                //space
                if (isset($_POST['space_chbox'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('space', true);
                }
                else $_SESSION['CG']->chsbuilder->set_a_need('space', false);

                //littles
                if (isset($_POST['littles_chbox'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('littles', implode([$_POST['l_first_sel'], $_POST['l_last_sel']]));
                }    
		else $_SESSION['CG']->chsbuilder->set_a_need('littles', false);

                //CAPS
                if (isset($_POST['caps_chbox'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('CAPS', implode([$_POST['c_first_sel'], $_POST['c_last_sel']]));
                }
                else $_SESSION['CG']->chsbuilder->set_a_need('CAPS', false);
               
                //digits
		if (isset($_POST['digits_chbox'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('digits', true);
                }
                else $_SESSION['CG']->chsbuilder->set_a_need('digits', false);

                //specials
		if (isset($_POST['specials_chbox'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('specials', true);
		}
                else $_SESSION['CG']->chsbuilder->set_a_need('specials', false);

                //excluded
                if (strlen($_POST['excluded_edit'])) {
                    $_SESSION['CG']->chsbuilder->set_a_need('exclude', $_POST['excluded_edit']);
                }
                else $_SESSION['CG']->chsbuilder->set_a_need('exclude', "");

                //building...
                $_SESSION['CG']->chsbuilder->build_from_needs();


            }

            catch (Exception $e) {

                phpAlert($e->getMessage());
                return;
            } 

            filelog("IN PREVIEWCAL POST, only for NEEDS: ". print_r($_POST, true));
            filelog("IN PREVIEWCAL POST, only for NEEDS: ". print_r($_SESSION['CG']->chsbuilder->show_needs(), true));
            filelog("in previewcall chsbuilderSNIFF: " . $_SESSION['CG']->chsbuilder->sniff_charsetstr());

            $form->set_value_by_id("ccharset_memo", $_SESSION['CG']->chsbuilder->sniff_charsetstr());

            $form->get_dom()->getElementByID("sendbtn")->removeAttribute("disabled");
            $form->get_dom()->getElementByID("shufflechset")->removeAttribute("disabled");


    }; 


$form_chset->add_submit_callable('previewchset', $preview_call);

//shuffle callable
$shuffle_call = function($form) {
    try {

        $_SESSION['CG']->chsbuilder->shuffle_charset();

        $form->set_value_by_id("ccharset_memo", $_SESSION['CG']->chsbuilder->sniff_charsetstr());

    }
    catch (Exception $e) {

        phpAlert($e->getMessage());
        return;
    } 
    

}; //please remember it's closure... (;)


$form_chset->add_submit_callable('shufflechset', $shuffle_call);

$form_chset_init = function($form) {

    $_SESSION['CG']->conform_needs_toreqs();
    filelog("IN F_CHSet_INIT: AFTER conform needs to reqs: " . print_r($_SESSION['CG']->chsbuilder->show_needs(), true));

    try {
        $_SESSION['CG']->chsbuilder->build_from_needs();
    }
    catch (Exception $e) {
        phpAlert($e->getMessage());
        return;
    } 
   

    $form->set_inner_text($form->get_dom()->getElementByID("ccharset_memo"), $_SESSION['CG']->chsbuilder->sniff_charsetstr());
    
    filelog("IN F_CHSet_INIT: before conform needs to reqs: " . print_r($_SESSION['CG']->chsbuilder->show_needs(), true));


    //setting needs controls
    if (!$_SESSION['CG']->chsbuilder->check_needs_empty()) return; //only space and/or excluded

    filelog("IN F_CHSet_INIT: before setting needs controls: " . print_r($_SESSION['CG']->chsbuilder->show_needs(), true));
    
    //space
    if ($_SESSION['CG']->chsbuilder->get_a_need('space'))
        $form->set_attr_novalue("space_chbox", "checked");
    else
        $form->get_dom()->getElementByID("space_chbox")->removeAttribute("checked");

    //littles
    if ($need_littles =  $_SESSION['CG']->chsbuilder->get_a_need('littles')) {
            $form->set_attr_novalue("littles_chbox", "checked");
            $form->set_child_attr_by_chvalattr("l_first_sel", "option", $need_littles[0], "selected", "selected");
            $form->set_child_attr_by_chvalattr("l_last_sel", "option", $need_littles[-1], "selected", "selected");
            
        }
    else {
    $form->get_dom()->getElementByID("littles_chbox")->removeAttribute("checked");
    $form->set_attr_novalue("l_first_sel", "disabled");
    $form->set_attr_novalue("l_last_sel", "disabled");

    filelog("IN F_CHSet_INIT: after setting trying to uncheck littles...");
    } 

    //CAPS
    if ($need_CAPS =  $_SESSION['CG']->chsbuilder->get_a_need('CAPS')) {
            $form->set_attr_novalue("caps_chbox", "checked");
            $form->set_child_attr_by_chvalattr("c_first_sel", "option", $need_CAPS[0], "selected", "selected");
            $form->set_child_attr_by_chvalattr("c_last_sel", "option", $need_CAPS[-1], "selected", "selected");
            
        }
    else {
    $form->get_dom()->getElementByID("caps_chbox")->removeAttribute("checked");
    $form->set_attr_novalue("c_first_sel", "disabled");
    $form->set_attr_novalue("c_last_sel", "disabled");
    filelog("IN F_CHSet_INIT: after setting trying to uncheck caps...");
    } 

    //digits
    if ($_SESSION['CG']->chsbuilder->get_a_need('digits'))
        $form->set_attr_novalue("digits_chbox", "checked");
    else
        $form->get_dom()->getElementByID("digits_chbox")->removeAttribute("checked");

    //specials
    if ($_SESSION['CG']->chsbuilder->get_a_need('specials'))
        $form->set_attr_novalue("specials_chbox", "checked");
    else
        $form->get_dom()->getElementByID("specials_chbox")->removeAttribute("checked");

    //exclude
    $need_exclude = $_SESSION['CG']->chsbuilder->get_a_need('exclude');
    if (strlen($need_exclude))
        $form->get_dom()->getElementByID("excluded_edit")->setAttribute("value", $need_exclude);
    else
        $form->get_dom()->getElementByID("excluded_edit")->setAttribute("value", "");

};


$form_chset->set_init_callable($form_chset_init);



?>
