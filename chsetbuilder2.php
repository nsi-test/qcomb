<?php

class CHSBuilder {

    private $needs; //assoc array, etc.

    private $produce_case; //boolean (1.1)

    function __construct() {


        $this->CHSETSTR = "";

        //$this->ASCII_PRINT95 = " !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";
       //1.1 deprecated, see below


        //1.1 assoc optimisation
        $this->chars = array_flip(['littles', 'CAPS', 'digits', 'specials', 'space', 'excluded']);

	$this->chars['littles'] = range('a', 'z');
	$this->chars['CAPS'] = range('A', 'Z');
	$this->chars['digits'] = range('0 ', '9 '); //the old way - '0 '... is ok
	$this->chars['specials'] = str_split("!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~");
	$this->chars['space'] = array(" ");
	$this->chars['excluded'] = ""; //'excluded' (!) not 'exclude'  


        //1.1 dev change
        //$this->ascii_full = array_merge($this->space, $this->littles, $this->CAPS, $this->digits, $this->specials);
        $this->ascii_full = array_merge($this->chars['space'], $this->chars['littles'], $this->chars['CAPS'], $this->chars['digits'], $this->chars['specials']); //no foreach, I want to see the order

        $this->ASCII_PRINT95 = implode($this->ascii_full);

        echo strlen($this->ASCII_PRINT95);

        $this->produce_case = false;
        //1.1

	//?
        $this->needs = array_flip(array('littles', 'CAPS', 'digits', 'specials', 'space', 'exclude'));

        foreach ($this->needs as $k=>$val) $this->requirements[$k] = Array();

        //former - all false
	$this->needs['littles'] = 'az'; //the idea is string of 2 - true
	$this->needs['CAPS'] = 'AZ'; //the idea is string af 2 - true
	$this->needs['digits'] = true; //plain boolean
	$this->needs['specials'] = true; //plain boolean
	$this->needs['space'] = false; //plain boolean
	$this->needs['exclude'] = ""; //exclude string 

        $this->build_from_needs();

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
        

         if (in_array($start, $this->chars['digits']) and !in_array($end, $this->chars['digits'])) throw new Exception("Wrong character");
         if (in_array($start, $this->chars['littles']) and !in_array($end, $this->chars['littles'])) throw new Exception("Wrong character");
         if (in_array($start, $this->chars['CAPS']) and !in_array($end, $this->chars['CAPS'])) throw new Exception("Wrong character");
 
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

        $candCHSETSTR = $this->CHSETSTR . implode($this->chars['specials']);

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_specials!");

        return $this->chars['specials'];

    }

    function get_digits() {

        $candCHSETSTR = $this->CHSETSTR . implode($this->chars['digits']);

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_digits!");

        return $this->chars['digits'];

    }

    function get_space() {

        $candCHSETSTR = $this->CHSETSTR . " ";

        if (!$this->check_duplicate($candCHSETSTR)) $this->CHSETSTR = $candCHSETSTR;
        else throw new Exception("chsbuilder duplicates in get_space!");

        return str_split(" ");

    }


    function set_excluded($exclstr, $conforming=false) {

        if ($this->check_duplicate($exclstr)) {
            $this->needs['exclude'] = "";
            throw new Exception("Duplicate characters exist for exclision in chsbuilder!");
        }

        
        if (!$conforming && $this->check_exclude_toomuch($exclstr)) {
            $this->needs['exclude'] = "";
            throw new Exception("Cannot put in excluded whole character class! Use requirement check boxes instead.");
        } 
        //have to be revised the whole idea - conform_chset_toreqs uses set_excluded and excl whole classes

        $this->chars['excluded'] = $exclstr;

    }

    function exclude_them() {

        foreach (str_split($this->chars['excluded']) as $exchar) {

           $this->CHSETSTR =  str_replace($exchar, "", $this->CHSETSTR);

        }

    }

    //analogue of check_duplicate
    function check_exclude_toomuch($excl_str) {

        //malko se pvtr amashte vidq
        $excl_array = str_split($excl_str);

        sort($excl_array);

        $excl_chsorts = array_flip(array('littles', 'CAPS', 'digits', 'specials', 'space'));
        
        foreach ($excl_chsorts as $k=>$val) $excl_chsorts[$k] = [];

        //here improvment -  (assoc ar and cycle)->

        foreach ($excl_array as $exclchar) {

            //sets the excl sets by chsorts
            foreach ($this->chars as $k=>$chsort) {
                if ($k == 'excluded') continue;
                if (in_array($exclchar, $chsort)) array_push($excl_chsorts[$k], $exclchar); 
            }
           
        }

    //then -  excl_chsorts, to string , compare with chars['...'] (may be can be esear?) 
        foreach ($excl_chsorts as $k=>$excl_chsort) {
            sort($excl_chsort);
            if ($excl_chsort === $this->chars[$k]) return true; //exclude too much - yes
        }

        return false; //not too much - ok

    }

    //function set_order() //not now

    function check_duplicate($candidate_str) { //quite important, but only check and true/false
        $cand_arr = str_split($candidate_str);
        if (count($cand_arr) > count(array_unique($cand_arr))) return true; //duplacates - yes
        return false; //no duplicates - ok 
    }

    
    //givers: have to give arrays for dropdowns usage, etc.):

    function give_littles() {
        return $this->chars['littles'];

    }

    function give_CAPS() {
        return $this->chars['CAPS'];

    }

    function give_specials() {

        return $this->chars['specials'];

    }

    function give_digits() {

        return $this->chars['digits'];

    }

    function give_space() {

        return str_split(" ");  //" ";

    }

//\"givers" are necessary for other things too -  - impose of requirements

    function get_charsetstr() {

        if (strlen($this->CHSETSTR) === 0) $this->CHSETSTR = $this->ASCII_PRINT95; //the idea is when we work only with reqs
                                                                      //i.e. without get... etc. (get ALL)

        return $this->CHSETSTR;
    }


    function sniff_charsetstr() {return $this->CHSETSTR;} //for debug purpuses, showing, etc...


    function reset_charsetstr() { //kind of start anew

        $this->CHSETSTR = "";
    }


    //1.1

    function give_excluded() {return $this->chars['excluded'];} //not obligitory really excl (to show ...)

    function build_for_id_produce() { //these is  what producing need firmly

        $this->reset_charsetstr(); //start from cleaan state
 
        //build...
 
        $this->set_a_need('space', false); //set the needs fo form_chset after that
 
        $this->set_a_need('littles', 'az');
  
        $this->set_a_need('CAPS', 'AZ');
 
        $this->set_a_need('digits', true);
 
        $this->set_a_need('specials', false);
 
        $this->set_a_need('exclude', "hijlnoIJOQ01"); //hard

        $this->build_from_needs(); //building...
  
        $this->produce_case = true; //(!)

    } //build_for_...

    function is_produce_case() { 

        if ($this->produce_case) return true;
        else return false; 

    } 

    function not_a_produce_case($no_produce=true) {
        if ($no_produce) $this->produce_case = false;
        else $this->produce_case = true; 
}


    //desperate dynamic...

    function set_needs_from_string($userstring) {

        //build needs

        $userarray = str_split($userstring);

        sort($userarray);

        $user_chsorts = array_flip(array('littles', 'CAPS', 'digits', 'specials', 'space', 'exclude'));
        
        foreach ($user_chsorts as $k=>$val) $user_chsorts[$k] = [];

        //here improvment -  (assoc ar and cycle)->

        foreach ($userarray as $uschar) {

            //sets the user sets by chsorts
            foreach ($this->chars as $k=>$chsort) {
                if ($k == 'excluded') continue;
                if (in_array($uschar, $chsort)) array_push($user_chsorts[$k], $uschar); 
            }
           
        }

        $this->needs["exclude"] = ""; 

        if ($user_chsorts["littles"]) {
            $this->needs["littles"] = reset($user_chsorts['littles']) . end($user_chsorts['littles']);
            //diff between start-end & the real chars
            $this->needs["exclude"] .= implode(array_diff(range(reset($user_chsorts['littles']), end($user_chsorts['littles'])), $user_chsorts["littles"]));
        }
        else $this->needs["littles"] = '';

        if ($user_chsorts["CAPS"]) {
            $this->needs["CAPS"] = reset($user_chsorts['CAPS']) . end($user_chsorts['CAPS']);
            $this->needs["exclude"] .= implode(array_diff(range(reset($user_chsorts['CAPS']), end($user_chsorts['CAPS'])), $user_chsorts["CAPS"]));
        } 
        else $this->needs["CAPS"] = '';

        if ($user_chsorts["digits"]) {
            $this->needs["digits"] = true; 
            //diff between all & the real chars
            $this->needs["exclude"] .= implode(array_diff($this->chars['digits'], $user_chsorts["digits"]));
        }
        else $this->needs["digits"] = false;

        if ($user_chsorts["specials"]) {
            $this->needs["specials"] = true; 
            $this->needs["exclude"] .= implode(array_diff($this->chars['specials'], $user_chsorts["specials"]));
        }
        else $this->needs["specials"] = false;

        if ($user_chsorts["space"]) //no exclude here
            $this->needs["space"] = true; 
            else $this->needs["space"] = false;

        $this->CHSETSTR = $userstring; //shuffle analogy

    } //set_needs_from_string

    //dd


  //\1.1

} //CHSBuilder







?>



