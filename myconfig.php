<?php

class MyConfig {

    private $dom;
    private $config_root;
    private $file_name;

    function __construct($file_name="config.xml") {

        $this->file_name = $file_name;
        //not very clear, but this name is "real" only after read_or_create() or open_file()

        $impl = new DOMImplementation();

        $dtd = $impl->createDocumentType('XML', '', '');

        $this->dom = $impl->createDocument('', '', $dtd);

        $this->dom->encoding = 'UTF-8';

        $this->config_root = $this->dom->appendChild($this->dom->createElement('config'));
     
        $this->config_root->appendChild(new DOMText("\n"));

        $this->xpath = new DOMXPath($this->dom);

    }

    //function get_dom() {return $this->dom;} //for debugging, this dom doc is not necesarry outside

    function set_option($name, $value) {

        $query = $name;
        $elements = $this->xpath->query($query, $this->config_root);
	//more than 1 - bad conf
        if ($elements->length > 1) throw new Exception("more than one same tag option - incompatible!");

	if ($elements->length === 1) $element = $elements[0];
	else { 
            $element = $this->config_root->appendChild(new DOMText("  "));
            $element = $this->config_root->appendChild($this->dom->createElement($name));
            $this->config_root->appendChild(new DOMText("\n"));
        }

        //more than 1 - bad conf
        if ($element->childNodes->length > 1) throw new Exception("more than one option subelemnts - incompatible!");

        if ($element->childNodes->length === 1) {
            $toremove = $element->childNodes[0];
            $element->removeChild($toremove);
        }

        $element->appendChild(new DOMText($value));

    }

    function get_option($name) {
        
        $query = $name;
        $elements = $this->xpath->query($query, $this->config_root);

	//more than 1 - bad conf
        if ($elements->length > 1) throw new Exception("more than one same tag option - incompatible!");

	if ($elements->length === 1) $element = $elements[0];
        else throw new Exception("no such option!");

        if ($element->childNodes->length > 1) throw new Exception("more than one option subelemnts - incompatible!");

        if ($element->childNodes->length === 1) return $element->childNodes[0]->wholeText;
	else   return  ""; //empty
        

    }

    function get_filename() {return $this->file_name;}

    function get_options() {
        $options = [];
	foreach ($this->config_root->childNodes as $option)  {
            if ($option->nodeName == '#text') continue;
            $options[$option->nodeName] = $option->nodeValue;
        }
        return $options;

    }

    function save_file() {

        $conffile = fopen($this->file_name, "w") or die("Unable to open file!");

        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        $content =  $this->dom->saveXML();

        fwrite($conffile, $content);

        fclose($conffile);

    }

    function open_file($file_name) {

        try {
            $this->dom->load($file_name);
        }
	catch (Exception $e) {
            echo $e->getMessage();
            return;
        } 

        $this->file_name = $file_name;
        $this->config_root = $this->dom->childNodes[1]; //XML is first
        $this->xpath = new DOMXPath($this->dom);

    }


    function read_or_create() {

        if (file_exists($this->file_name)) {
            $this->open_file($this->file_name);
            return;
        }

        $this->set_option('log_to_file', '0');
        $this->save_file();

    } 


}	




?>
