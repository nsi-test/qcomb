<?php

class CHSBuilder {

    private $needs; //assoc array, etc.

    function __construct() {


        $this->CHSETSTR = "";

        $this->ASCII_PRINT95 = " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";

        echo strlen($this->ASCII_PRINT95);



        $this->digits = range('0 ', '9 '); //space is for php 7, not tested with 5.6
        $this->littles = range('a', 'z');
        $this->CAPS = range('A', 'Z');
        $this->specials = str_split("!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~");
        $this->space = array(" ");
        $this->excluded = "";


	//?
        $this->needs = array_flip(array('littles', 'CAPS', 'digits', 'specials', 'space', 'exclude'));

        foreach ($this->needs as $k=>$val) $this->requirements[$k] = Array();

	$this->needs['littles'] = false; //the idea is string of 2 - true
	$this->needs['CAPS'] = false; //the idea is string af 2 - true
	$this->needs['digits'] = false; //plain boolean
	$this->needs['specials'] = false; //plain boolean
	$this->needs['space'] = false; //plain boolean
	$this->needs['exclude'] = ""; //exclude string 

    }



    function set_a_need($ch_sort, $need) {
 
        //plain boolean
	if (gettype($need) === 'boolean') { 
            $this->needs[$ch_sort] = $need;
            return;
        }

	if ($ch_sort === 'exclude') {
            $this->needs[$ch_sort] = $need; //string
            return;
	}
	
        //chr
	if (in_array($ch_sort, ['littles', 'CAPS'])) {
            if ($need && strlen($need) != 2) throw new Exception("Incorrect character range string.");
            $this->needs[$ch_sort] = $need; //2 char string or false
        }

    }


    function get_a_need($ch_sort) {

        return $this->needs[$ch_sort];

    }

    function check_needs_empty() {
        foreach ($this->needs as $ch_sort=>$need) {
            if (!($ch_sort === 'exclude' || $ch_sort === 'space') && $need) return true;
        }

        return false; //only exclude or/and space

    }

    function show_needs() {
        
        return $this->needs;
    }

    function build_from_needs() {

        $this->reset_charsetstr();

	if ($this->get_a_need('space')) $this->get_space(); //like in preview in chset form

        if ($need_littles =  $this->get_a_need('littles')) $this->get_ordinary($need_littles[0], $need_littles[-1]);
        
        if ($need_CAPS =  $this->get_a_need('CAPS')) $this->get_ordinary($need_CAPS[0], $need_CAPS[-1]);

	if ($this->get_a_need('digits')) $this->get_digits();

	if ($this->get_a_need('specials')) $this->get_specials();

        $need_exclude = $this->get_a_need('exclude');
	if (strlen($need_exclude)) {
            $this->set_excluded($need_exclude);
            $this->exclude_them();
	}

	if (!$this->check_charsetstr_ok()) {
            $this->CHSETSTR = $this->ASCII_PRINT95;
            throw new Exception("Bad character number (0/1)!");
        }

    }

    function check_charsetstr_ok() {

        if (strlen($this->CHSETSTR) <=1) return false;
        return true;

    }

    function shuffle_charset() {
        if (!$this->check_charsetstr_ok()) throw new Exception("bad or missing charset when trying to shuffle");

        $chset_arr = str_split($this->CHSETSTR);
        //shuffle($chset_arr); //standart php func -not crypto secure
        $this->FYshuffle($chset_arr); //my func - crypto secure (see)
	$this->CHSETSTR = implode($chset_arr);
    }


    function FYshuffle(&$items) {
    //thanks to Andre Laszlo on SOF
    //Fisher-Yates algorithm
    //crypto secure because of random_int(), which is cs
        $items = array_values($items);
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $tmp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $tmp;
        }
    }
//older part...

    function get_ordinary($start, $end) {
        

         if (in_array($start, $this->digits) and !in_array($end, $this->digits)) throw new Exception("Wrong character");
         if (in_array($start, $this->littles) and !in_array($end, $this->littles)) throw new Exception("Wrong character");
         if (in_array($start, $this->CAPS) and !in_array($end, $this->CAPS)) throw new Exception("Wrong character");
 
         if (ord($end) - ord($start) < 0) throw new Exception("Wrong character order!");
         
         $candCHSETSTR = $this->CHSETSTR . implode(range($start, $end));

         if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
         else throw new Exception("chsbuilder duplitates in get_ordinary!");
         
	 return range($start, $end); //return string is deprecated, it's array
                                      //bellow the same

    }

    function get_littles() {return $this->get_ordinary('a', 'z');} //only for combgenerator

    function get_CAPS() {return $this->get_ordinary('A', 'Z');} //only for combgenerator


    function get_specials() {

        $candCHSETSTR = $this->CHSETSTR . implode($this->specials);

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_specials!");

        return $this->specials;

    }

    function get_digits() {

        $candCHSETSTR = $this->CHSETSTR . implode($this->digits);

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_digits!");

        return $this->digits;

    }

    function get_space() {

        $candCHSETSTR = $this->CHSETSTR . " ";

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_space!");

        return str_split(" ");

    }


    function set_excluded($exclstr) {

        if (!$this->check_duplicate($exclstr)) $this->excluded = $exclstr;
        else throw new Exception("Duplicate characters exist for exclision in chsbuilder!"); 

    }

    function exclude_them() {

        foreach (str_split($this->excluded) as $exchar) {

           $this->CHSETSTR =  str_replace($exchar, "", $this->CHSETSTR);

        }

    }

    //function set_order() //cannot see it soon

    function check_duplicate($candidate_str) { //quite important, but only check and true/false
        $cand_arr = str_split($candidate_str);
        if (count($cand_arr) > count(array_unique($cand_arr))) return true; //duplacates - yes
        return false; //no duplicates - ok 
    }

    
    //givers: have to give arrays for dropdowns usage, etc.):

    function give_littles() {
        return $this->littles;

    }

    function give_CAPS() {
        return $this->CAPS;

    }

    function give_specials() {

        return $this->specials;

    }

    function give_digits() {

        return $this->digits;

    }

    function give_space() {

        return str_split(" ");  //" ";

    }

//\"givers" are necessary for other things too -  - impose of requirements

    function get_charsetstr() {

        if (!$this->CHSETSTR) $this->CHSETSTR = $this->ASCII_PRINT95; //the idea is when we work only with reqs
                                                                      //i.e. without get... etc. (get ALL)

        return $this->CHSETSTR;
    }


    function sniff_charsetstr() {return $this->CHSETSTR;} //for debug purpuses, showing, etc...


    function reset_charsetstr() { //kind of start anew

        $this->CHSETSTR = "";
    }





} //CHSBuilder













?>



