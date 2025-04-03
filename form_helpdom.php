<?php


//form help

require_once "help_txt.php";

$form_help = new MyFormDom($htd, "form_help", $_SERVER['REQUEST_URI']); //8.x compat

$form_help->add_text_element('h1', 'QComb  ' . QC_VERSION);
$h_header = $form_help->add_text_element('h2', 'Help');
$h_header->setAttribute("style", "position:relative; left:140px; font-weight:bold;");


$form_help_init = function($form) {

    $form->add_form_page_title("QComb " . QC_VERSION . " - HELP");

}; //closure

$form_help->set_init_callable($form_help_init);




$help_ordered_list = $form_help->add_sametype_element("ol", "help_ordered_list");


$num_list = [];

foreach ($item_list as $num_item) {

    $num_list[] = $num_item[0];

}



$form_help->add_children_from_array("help_ordered_list", "li", $num_list, false, "b");





foreach ($help_ordered_list->childNodes as $k=>$num_li) {

    $help_unordered_list = $form_help->add_sametype_element("ul", "help_unordered_list".$k);

    
    $form_help->add_children_from_array("help_unordered_list".$k, "li", $item_list[$k][1], false);

    foreach ($item_list[$k][1] as $j=>$unumline) {

        if (substr($unumline, 0, 1) === '^')  {            
          $style_el = $form_help->set_child_attr_by_chindex("help_unordered_list".$k, "li", $j, "style", "list-style-type:none;"); 
          $form_help->el_replace_inner_text($style_el, preg_replace('/^\^{1}/', '',$unumline)); 
        }

        if (substr($unumline, 0, 1) === '=')  {            
          $style_el = $form_help->set_child_attr_by_chindex("help_unordered_list".$k, "li", $j, "style", "list-style-type:none; padding-left: 1.5em;"); 
          preg_match('/\*.+\"/', $unumline, $mtar);
          $first_bold = $style_el->appendChild($form_help->get_dom()->createElement("b"));
          $form_help->set_inner_text($first_bold, $mtar[0]);
          $form_help->el_replace_inner_text($style_el, preg_replace('/^={1}\*.+\"/', '',$unumline)); 

        } //if substr

        //link
        $unumline_ar = explode(':;', $unumline);
        if (count($unumline_ar) > 1) {
            $link_el = $form_help->get_child_el_by_chindex("help_unordered_list".$k, "li", $j);
            $form_help->el_replace_inner_text($link_el, "");
            foreach ($unumline_ar as $n=>$unuml_part) {
               if (!($n % 2)) {$form_help->set_inner_text($link_el, $unuml_part);}
               else {
                   $link = $form_help->add_text_element('a', $unuml_part); 
                   $link->setAttribute('href', '');
                   $link->setAttribute('onclick', 'newlinkOpen("' . $unuml_part . '"); return false;'); //false - not to refresh parent
                   //my res: 1920x1080
                   $link_el->appendChild($link);
               } //if else
            } //foreach

        } //if count

    } //foreach $item_list

   
    $num_li->appendChild($help_unordered_list);


}

$form_help->remove_me_from_dom(); //8.x compat

?>
