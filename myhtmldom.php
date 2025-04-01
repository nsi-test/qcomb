<?php



class MyHtmlDom {


    public $dom; //DOM document //8.x compat - public
    public $head, $body; 
    

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

    function add_css_link($href) {
        $script_el =  $this->head->appendChild($this->dom->createElement('link'));
        $script_el->setAttribute("rel", "stylesheet");
        $script_el->setAttribute("href", $href);
    }


    function add_page_title($title) {
        $title_el =  $this->head->appendChild($this->dom->createElement('title'));
        $title_el->appendChild(new DOMText($title));
    }

} //class


class MyFormDom {

	private $htd; //8.x compat
    private $dom; //corresponding
    private $form;
    private $id;
    private $submit_callables; //asc array
    private $input_tags; //array
    private $init_callables; //array, too complicated otherwise
    private $helper_callables; //asc array
    private $get_string; //same as in the maindom case (not in constructor - set in function)

    private $dom_head; //1.1

    function __construct($htd, $id, $action, $method = "post") { //8.x compat

        $this->id = $id;

		$this->htd = $htd; //8.x compat

		$this->dom = $htd->dom; //8.x compat, former $this->dom = $dom;
        
        $this->dom_head = $this->dom->getElementsByTagName("head")[0]; //beleave it's only one

        $this->form = $this->dom->createElement("form");

		$this->htd->body->appendChild($this->form); //8.x compat, crucial

        $this->form->setAttribute("id", $this->id);

        $this->form->setIdAttribute("id", true); //important

        $this->form->setAttribute("method", $method);

        $this->form->setAttribute("action", $action); 

        $this->add_newline();

        $this->submit_callables = array();

        $this->init_callables = array();

        $this->helper_callables = array();

        $this->input_tags = array("input", "select", "textarea");


    } //MyFormDom constructor


    function remove_me_from_dom() { //8.x compat function important
        $this->htd->body->removeChild($this->form);
    }

    //no display - it is in dom class

    function get_form() {
        return $this->form;
    }

    function get_dom() {
        return $this->dom;
    }

    //1.1
    function add_form_page_title($title) {
        $title_el =  $this->dom_head->appendChild($this->dom->createElement('title'));
        $title_el->appendChild(new DOMText($title));
    }

    function set_get_string($get_string) {
        $this->get_string = $get_string;
    }

    function get_get_string() { //supid a little...
        return $this->get_string;
    }
    
    //\1.1

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

    function add_id_to_element($element, $id) {
        if ($this->dom->getElementById($id)) return;
        $element->setAttribute("id", $id);    
        $element->setIdAttribute("id", true); //important
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

    function add_inner_text_by_id($id, $text) {
        $element = $this->dom->getElementById($id);
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


    function el_replace_inner_text($element, $text) {
        if ($element->childNodes->length) {
            $toremove = $element->childNodes[0];
            $element->removeChild($toremove);
        }
        $element->appendChild(new DOMText($text));
    }

    function append_child_to_parent($parent_element_id, $child_element) { //this when we've got only the form and parent id
        $parent_element = $this->dom->getElementById($parent_element_id);
        $parent_element->appendChild($child_element);
    }


    function add_children_from_array($id, $chtag, $chvalues, $samevalue=true, $inneltag = "") { //no same value - make help
    //$inneltag - element in the element
        $element = $this->dom->getElementById($id);
        foreach ($chvalues as $childvalue) {
            $child = $element->appendChild($this->dom->createElement($chtag));
            if ($samevalue) {
                $child->setAttribute("value", $childvalue);
            }
            if ($inneltag === "") {
                $this->set_inner_text($child, $childvalue);
                $this->add_newline();
            }
            else {
               $innchild = $child->appendChild($this->dom->createElement($inneltag));
               $this->set_inner_text($innchild, $childvalue);
            }

        }
        $this->add_newline();

    }

    function set_child_attr_by_chindex($id, $chtag, $chindex, $attr, $value) { //better edition

        $thechild = $this->get_child_el_by_chindex($id, $chtag, $chindex);

        $thechild->setAttribute($attr, $value);

        return $thechild;

    }

    function set_child_attr_by_chvalattr($id, $chtag, $valattrvalue, $attr, $value) {

        $element=$this->dom->getElementById($id);

        $children = $element->getElementsByTagName($chtag);

        foreach ($children as $child) {
            if ($child->getAttribute("value") === $valattrvalue) $child->setAttribute($attr, $value);
        
            else $child->removeAttribute($attr);

        }

    }

    function get_child_el_by_chindex($id, $chtag, $chindex) { //same as above, just return element

        $element=$this->dom->getElementById($id);

        $children = $element->getElementsByTagName($chtag);

        if ($chindex >= $children->length) throw new Exception("child index out of range!");

        return $children[$chindex];

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
            //1.1
            foreach ($row as $cell_data) {
	        $cell_element = $row_element->appendChild($this->dom->createElement("td"));
	        $this->set_inner_text($cell_element, $cell_data);
            } 
            //\1.1

	} //foreach $rows_array $index=>$row
    } //populate f-n 


    function add_submit_callable($submit_id, $func) {
        $this->submit_callables[$submit_id] = $func;
    }

    function set_init_callable($func) {
        $this->init_callables[] = $func; //because it's easier
        //equivalent mostly to array_push

    }

    function add_helper_callable($helper_id, $func) {
        $this->helper_callables[$helper_id] = $func;
    }


    function post_set($POST) {
        filelog("IN post_set of form " . $this->form->getAttribute("id") . " _POST: " . print_r($POST, true));
        foreach ($this->input_tags as $itag) {
            foreach ($this->form->getElementsByTagName($itag) as $finputable) {
                if ($POST[$finputable->getAttribute("name")]) {

		    if ($finputable->getAttribute("type") === "checkbox" || $finputable->getAttribute("type") === "radio") { //radio
	                $finputable->setAttribute("checked", ""); //check it
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
                elseif ($finputable->getAttribute("type") === "checkbox" || $finputable->getAttribute("type") === "radio") { //radio
                    $finputable->removeAttribute("checked"); //should uncheck...   
                }

            }
        }
        
        //for the dynamic jquery submit (not necessary)

        if (isset($POST["nonexistinginput"])) $submitter = $POST["nonexistinginput"];

        //for dynam sumb


        filelog("submitter: $submitter");
	if ($submitter) $_SESSION['LAST_SUBMIT_NAME'] = $submitter;
        else $_SESSION['LAST_SUBMIT_NAME'] = 'EMPTY';

	filelog("in post_set, LAST_SUBMIT_NAME: " . $_SESSION['LAST_SUBMIT_NAME'] . "\n");

        if ($this->submit_callables[$submitter]) $this->submit_callables[$submitter]($this); 

            
    }


    function init_call() {

        foreach ($this->init_callables as $init_callable) $init_callable($this); //1.1 successive call of inits

    }

    function helper_call($helper_id) {
        $this->helper_callables[$helper_id]($this);
    }

    function submit_call($submit_id) {
        if ($this->submit_callables[$submit_id]) $this->submit_callables[$submit_id]($this); 
    }



    function reset_all() {
        reset_it($this->get_string);
    }


} //MyFormDom




?>
