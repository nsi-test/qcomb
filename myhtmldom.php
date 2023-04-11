<?php

class MyHtmlDom {


    protected $dom; //DOM document
    protected $head, $body; 
    

    function __construct() {

        $impl = new DOMImplementation();

        $dtd = $impl->createDocumentType('HTML', '', '');

        $this->dom = $impl->createDocument('', '', $dtd);

 

        $this->html = $this->dom->appendChild($this->dom->createElement('html'));

        $this->head = $this->html->appendChild($this->dom->createElement('head'));

        $this->head->appendChild(new DOMText("\n"));

        $this->CSS = $this->head->appendChild($this->dom->createElement('style'));

        $this->body = $this->html->appendChild($this->dom->createElement('body'));

        $this->body->appendChild(new DOMText("\n"));

        $this->body->appendChild($this->dom->createElement('p', 'asd'));
        

    }




    function display() {

        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

          

        return $this->dom->saveHTML();
    }


    function replace_or_add_form($newF) {    

        $oldF = $this->dom->getElementsByTagName("form")[0];

        if ($oldF) { 
            $this->dom->body->replaceChild($newF, $oldF);
            return;
        }

        $this->body->appendChild($newF);

    }

    function get_dom() { 
        return $this->dom;
    } 

    function get_head() { 
        return $this->head;
    } 

    function get_body() { 
        return $this->body;
    } 

    function add_CSS($style_text) { //I assume it's in head (?)   
        $this->CSS->appendChild(new DOMText("\n"));
        $this->CSS->appendChild(new DOMText($style_text));
        $this->CSS->appendChild(new DOMText("\n"));
    }

    function add_script_src($src) {
        $script_el =  $this->head->appendChild($this->dom->createElement('script'));
        $script_el->setAttribute("src", $src);
}


}


class MyFormDom {

    private $dom; //corresponding
    private $form;
    private $id;
    private $submit_callables; //asc array
    private $input_tags; //array
    private $init_callables; //array, too complicated otherwise

    function __construct($dom, $id, $action, $method = "post") {

        $this->id = $id;

        $this->dom = $dom;

        $this->form = $this->dom->createElement("form");

        $this->form->setAttribute("id", $this->id);

        $this->form->setIdAttribute("id", true); //important

        $this->form->setAttribute("method", $method);

        $this->form->setAttribute("action", $action); 

        $this->add_newline();

        $this->submit_callables = array();

        $this->init_callables = array();

        $this->input_tags = array("input", "select", "textarea");


    }


    //no display - it is in dom class

    function get_form() {
        return $this->form;
    }

    function get_dom() {
        return $this->dom;
    }


    function add_br() {
        $this->form->appendChild($this->dom->createElement('br'));
        $this->add_newline(); 
    }

    function add_newline() {
        $this->form->appendChild(new DOMText("\n"));
    }

    function add_text($text) {
        $this->form->appendChild(new DOMText($text));
        $this->add_newline(); 
    }

    function add_text_element($tag, $text) { //p, h123, etc.
        $text_element = $this->form->appendChild($this->dom->createElement($tag, $text));
        $this->add_newline();
        return $text_element; 
    }

    function add_entity_ref($ename) {
        $this->form->appendChild(new DOMEntityReference($ename));
    }


    function add_sametype_element($type, $id) {
        $sametype_element = $this->form->appendChild($this->dom->createElement($type));
        
        $sametype_element->setAttribute("type", $type);

        $sametype_element->setAttribute("id", $id);

        $sametype_element->setIdAttribute("id", true); //important

        $sametype_element->setAttribute("id", $id);

        $sametype_element->setAttribute("name", $id); //for getting from POST

        $this->add_newline();

        return $sametype_element;

    }


    function add_input_element($type, $id) {
        $input_element = $this->form->appendChild($this->dom->createElement("input"));
        
        $input_element->setAttribute("type", $type);

        $input_element->setAttribute("id", $id);

        $input_element->setIdAttribute("id", true); //important

        $input_element->setAttribute("name", $id); //for getting from POST

        $this->add_newline();

        return $input_element;

    }

    function set_attr_novalue($id, $name) { //better v.
        $element = $this->dom->getElementByID($id);
        $attr = $this->dom->createAttribute($name);
        $element->setAttributeNode($attr);
    }


    function set_inner_text($element, $text) { //in practice - add
        $element->appendChild(new DOMText($text));
    }

    function replace_inner_text($id, $text) {
        $element = $this->dom->getElementById($id);
        if ($element->childNodes->length) {
            $toremove = $element->childNodes[0];
            $element->removeChild($toremove);
        }
        $element->appendChild(new DOMText($text));
    }

    function add_children_from_array($id, $chtag, $chvalues) {
        $element = $this->dom->getElementById($id);
        foreach ($chvalues as $childvalue) {
            $child = $element->appendChild($this->dom->createElement($chtag));
            $child->setAttribute("value", $childvalue);
            $this->set_inner_text($child, $childvalue);
        }

    }

    function set_child_attr_by_chindex($id, $chtag, $chindex, $attr, $value) { //better edition

        $element = $this->dom->getElementById($id);

        $children = $element->getElementsByTagName($chtag);

        if ($chindex >= $children->length) throw new Exception("child index out of range!");

        $children[$chindex]->setAttribute($attr, $value);

    }

    function set_child_attr_by_chvalattr($id, $chtag, $valattrvalue, $attr, $value) {

        $element=$this->dom->getElementById($id);

        $children = $element->getElementsByTagName($chtag);

        foreach ($children as $child) {
            if ($child->getAttribute("value") === $valattrvalue) $child->setAttribute($attr, $value);
        
            else $child->removeAttribute($attr);

        }

    }


    function set_value_by_id($id, $value) { //the name is quite misguiding...
    
         $element = $this->dom->getElementById($id);

         if (!$element) throw new Exception("no element with such id!");

         $element_type = $element->getAttribute("type");

         filelog("in setvaluebyid problematic type: " . $element_type);
         filelog("in setvalue byid problematic value: " . $value);

         if ($element_type == "textarea") {
             $this->replace_inner_text($id, $value);
             return;
             }

 

    }

    
    function format_table_by_cols_array($id, $header_array) {   
        $table_element = $this->dom->getElementById($id);
        $header = $table_element->appendChild($this->dom->createElement("tr"));
	foreach ($header_array as $colname) {
            $colhead = $header->appendChild($this->dom->createElement("th"));
            $this->set_inner_text($colhead, $colname);
	}

    }

    function populate_table_from_array($id, $rows_array, $autonumber = false) {
        $table_element = $this->dom->getElementById($id);
	foreach ($rows_array as $index=>$row) {
            $row_element = $table_element->appendChild($this->dom->createElement("tr")); 
	    if ($autonumber) { 
		$cell_element = $row_element->appendChild($this->dom->createElement("td"));
		$this->set_inner_text($cell_element, strval(++$index));
            }
	    $cell_element = $row_element->appendChild($this->dom->createElement("td"));
	    $this->set_inner_text($cell_element, $row);

	}
    }


    function add_submit_callable($submit_id, $func) {
        $this->submit_callables[$submit_id] = $func;
    }

    function set_init_callable($func) {
        $this->init_callables[] = $func; //because it's easier

    }



    function post_set($POST) {
        filelog("IN post_set of form " . $this->form->getAttribute("id") . " _POST: " . print_r($POST, true));
        foreach ($this->input_tags as $itag) {
            foreach ($this->form->getElementsByTagName($itag) as $finputable) {
                if ($POST[$finputable->getAttribute("name")]) {

		    if ($finputable->getAttribute("type") === "checkbox") {
	                $finputable->setAttribute("checked", "checked");
			continue 1; //doesn't need a value
		    } 

                    if ($finputable->getAttribute("type") === "select") {
                        $this->set_child_attr_by_chvalattr($finputable->getAttribute("id"), "option", $POST[$finputable->getAttribute("name")], "selected", "selected");
			continue 1; //doesn't need a value..
                    }

                    if ($finputable->getAttribute("type") === "textarea") {
                        $this->replace_inner_text($finputable->getAttribute("id"), $POST[$finputable->getAttribute("name")]);
                        continue 1; //doesn't need a value..
                    }


                    
                    $finputable->setAttribute("value", $POST[$finputable->getAttribute("name")]); //sets value!!!  


                    if ($finputable->getAttribute("type") === "submit") $submitter = $finputable->getAttribute("name");
                }
                elseif ($finputable->getAttribute("type") === "checkbox") {
                    $finputable->removeAttribute("checked"); //should uncheck...   
                }

            }
        }
        
        filelog("submitter: $submitter");
	if ($submitter) $_SESSION['LAST_SUBMIT_NAME'] = $submitter;
        else $_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';

	filelog("in post_set, LAST_SUBMIT_NAME: " . $_SESSION['LAST_SUBMIT_NAME'] . "\n");

        if ($this->submit_callables[$submitter]) $this->submit_callables[$submitter]($this); 

            
    }


function init_call() {

    $this->init_callables[0]($this); //array, it's too complicated otherwise
    }


}


//MyFormDom




?>
