<?php


require_once "wcombin_gmp.php";
require_once "chsetbuilder2.php";

require_once "combfunc_giver.php"; //some funcs

class CombGenerator_GMP {

    private $wcomb_gmp; //WCombinGMP object
    public $chsbuilder; //CHSBuilder object

    private $cfunc_giver; //CFuncGiver object

    private $combcharnumb; //number of characters in combination (int)
    private $combnumb; //number of combinations (int)

    private $requirements; //array (assoc, complex)

    private $gen_settings; //array (assoc)

    private $outer_funcs; //assoc array //gotten from other obj


    function __construct() {

        $this->chsbuilder = new CHSBuilder();    

        //1.1 - trying to be ready at first... 
        $this->combcharnumb = 8;
	$this->combnumb = 10;

        $this->cfunc_giver = new CFuncGiver();

        //requirement aray defining start

        $this->requirements = array_flip(array('littles', 'CAPS', 'digits', 'specials', 'space'));

        foreach ($this->requirements as $k=>$val) $this->requirements[$k] = Array();
 
        foreach ($this->requirements as $k=>$ar) $this->requirements[$k]['req'] = true; //1.1 ..

        $this->requirements['space']['req'] = false;

        $chsgets = array("give_littles", "give_CAPS", "give_digits", "give_specials", "give_space"); //"give" (!)

        $gi = 0;
        foreach ($this->requirements as $k=>$ar) {
              $this->requirements[$k]['get_fns'] = $chsgets[$gi];
              $gi++;
        }

        foreach ($this->requirements as $k=>$ar) $this->requirements[$k]['res'] = false; 
       
        //requirements array defined

        $this->previous_requirements = $this->requirements;

        //previous requirements copied and the same


        $this->gen_settings['uniques_only'] = true;

        //1.1
        $this->gen_settings['map_from_id'] = false;

        $this->gen_settings['produce_from_id'] = 0; //new - values 1, 2, 0

        $this->outer_funcs['test'] = $this->cfunc_giver->give_func('test'); //of course it's not that one

        $this->outer_funcs['produced_combinations'] = $this->cfunc_giver->give_func('produced_combinations'); //the func
        //\1.1

    }

    //requirements comparison callback
    function cmp_reqs($a, $b) {
        if ($a['req'] === $b['req']) return 0;
        return ($a['req'] > $b['req'])? 1:-1;
    } 

    function reqs_differ_from_previous() {
       return array_udiff_assoc($this->requirements, $this->previous_requirements, [$this, 'cmp_reqs']);  
    }

    function copy_reqs_to_previous() {
        $this->previous_requirements = $this->requirements;
    }


//gen functions

    function insist_uniques($uniques = true) {
        $this->gen_settings['uniques_only'] = $uniques;    
    }

    function get_uniqueness() {
        return $this->gen_settings['uniques_only'];
    }

    //1.1
    function insist_id_map($id_map = true) {
        $this->gen_settings['map_from_id'] = $id_map;
    }

    function get_id_mapping() {
        return $this->gen_settings['map_from_id'];
    }

    //produce f-s
    function insist_id_produce($id_produce = 1) { //attention...
        if ($id_produce)
            $this->set_reqs_from_mask('00111'); //the reqs for this case
        //else
        //    $this->set_reqs_from_mask('01111'); //the reqs for standart case (bad idea)

        $this->gen_settings['produce_from_id'] = $id_produce; //whatever is there (0 is important)
    }

    function get_id_producing() {
        return $this->gen_settings['produce_from_id'];
    }
    //\1.1


//smooth functions (reqs, etc.)

    function set_reqs_from_mask($reqmask) {

        $reqmask = bindec($reqmask);
        if ($reqmask > 31) throw new Exception("Illegal requirements mask!");

        $i = 0;
        foreach ($this->requirements as $ch_sort=>$ar) {
            if ($reqmask & pow(2, $i)) {$this->requirements[$ch_sort]['req'] = true;}
            else {$this->requirements[$ch_sort]['req'] = false;} 
            $i++;
        }

    }

    function set_req_from_ch_sort($ch_sort, $breq=true) {
        $this->requirements[$ch_sort]['req'] = $breq;
    }

    function get_req_from_ch_sort($ch_sort) {
        return $this->requirements[$ch_sort]['req'];
    }

    function check_reqs_regularity() {
        if ($this->get_req_from_ch_sort('littles')) return true; 

        if ($this->get_req_from_ch_sort('CAPS')) return true; 

        if ($this->get_req_from_ch_sort('digits')) return true; 

        if ($this->get_req_from_ch_sort('specials')) return true; 

	return false; //means only space or nothing, which is wrong 
    }

    function get_requirements() {
        return $this->requirements;
    }

    function get_reqsort_number() {
        $reqsort_number = 0;
        foreach ($this->requirements as $ch_sort=>$ar) {
            if ($ar['req']) $reqsort_number++; 
        }
        return $reqsort_number;

    }


    function get_a_ch_sort($ch_sort) {

        $gfn = $this->requirements[$ch_sort]['get_fns'];

        return $this->chsbuilder->$gfn();
    }


    function check_requirements($combin) { //$combin is string

        $tempcombarr = str_split($combin);

        foreach ($tempcombarr as $ch) {
            foreach ($this->requirements as $k=>$ar) {

		    if ($ar['res'] && in_array($ch, $this->get_a_ch_sort($k))) {
			    continue 2; //check what it is
		    } 


		    if ($ar['req']) {
		        if (in_array($ch, $this->get_a_ch_sort($k))) {
                            $this->requirements[$k]['res'] = true; //$ch is ok
                            continue 2;
			}
			else {
			    continue;
		        }
                    }
                    else {//there is no req for that chsort
			if (in_array($ch, $this->get_a_ch_sort($k))) {
                            return false;
		        }		
			else {
		            continue;
		        } 
	            }

	    }
          
        }

    $req = $res = array();
    foreach ($this->requirements as $k=>$ar) {
        $req[] = $ar['req'];
        $res[] = $ar['res'];
    }

    if (array_diff_assoc($req, $res)) {
        return false; //there is  some not fullfilled req and all is false
    }

    return true;
    } //there shouldn't be much difference with charset (results in many iterations)


    function conform_chset_toreqs() {

        filelog("CONFRM_CHSTTOREQS BEGINS... \n");
        filelog("INITIAL CNFRM SNIFF::" . $this->chsbuilder->sniff_charsetstr() . "\n");

        //from 1.0
        $to_exclude = "";

        foreach ($this->requirements as $k=>$ar) {
            if (!$ar['req']) $to_exclude .= implode($this->get_a_ch_sort($k));
        }


        echo "DEBUG IN conformchsettoreqs to_exclude:" . $to_exclude . "\n";
        filelog("DEBUG IN conformchsettoreqs to_exclude::" . $to_exclude . "\n");

        filelog("IN CONFORM_chstoreqs - before chsb->set_excluded - sniff::" . $this->chsbuilder->sniff_charsetstr() . "\n");

        $this->chsbuilder->set_excluded($to_exclude, true); //$conforming=true

        $this->chsbuilder->exclude_them(); //exclude part end

	echo "DEBUG IN conformchsettoreqs after exclude chsb (length value):" . strlen($this->chsbuilder->sniff_charsetstr()) . " ";
        if (PHP_SAPI === 'cli') $this->chsbuilder->sniff_charsetstr() . "\n";
        else echo urlencode($this->chsbuilder->sniff_charsetstr());
        //from 1.0

        filelog("IN CONFORM_chstoreqs - AFTER exclude_them - sniff::" . $this->chsbuilder->sniff_charsetstr() . "\n");



        //for each ch class req - if yes add the ch class
        foreach ($this->requirements as $k=>$ar) {
            if ($ar['req'] && !array_intersect(str_split($this->chsbuilder->sniff_charsetstr()), $this->get_a_ch_sort($k))) {
            //if ($ar['req'] && !$sort_intersect_k) {
              
                filelog("IN COFRM, REQ INTRSCT: " . print_r(array_intersect(str_split($this->chsbuilder->sniff_charsetstr()), $this->get_a_ch_sort($k))), true);
                $agfn = str_replace("give", "get", $ar['get_fns']);
                $this->chsbuilder->$agfn(); //no return
            }
        }
        
        //setting it

        if (strlen($this->chsbuilder->sniff_charsetstr()) == 0) //the easy condition but later it shows... //not sure anymore...
            throw new Exception("You cannot have an empty set of requirements.");

	echo "DEBUG in conformchsettoreqs after add missing chsb: vnmv (length value)|" . strlen($this->chsbuilder->sniff_charsetstr()) . " ";
        if (PHP_SAPI === 'cli') echo $this->chsbuilder->sniff_charsetstr() . "\n";
        else echo urlencode($this->chsbuilder->sniff_charsetstr());

        filelog("CONFRM BEFORE setcharset... \n");
        $this->set_charset(); //once and in the end!
        filelog("CONFRM AFTER setcharset... \n");

    } //conform_chset_toreqs




    function conform_needs_toreqs() {
        foreach ($this->requirements as $k=>$ar) {
            if ($k === 'littles' && $ar['req'] && !$this->chsbuilder->get_a_need($k)) {
                $this->chsbuilder->set_a_need($k, "az");
		continue;
            }
            if ($k === 'CAPS' && $ar['req'] && !$this->chsbuilder->get_a_need($k)) {
                $this->chsbuilder->set_a_need($k, "AZ");
		continue;
            }
            if ($ar['req'] && $this->chsbuilder->get_a_need($k)) continue;
            $this->chsbuilder->set_a_need($k, $ar['req']);
        }

    }


//1.1

    function conform_reqs_toneeds() { //?
        foreach ($this->chsbuilder->show_needs() as $k=>$need) {
            if ($k === "exclude") continue;
            if ($need) $this->requirements[$k]['req'] = true;
            else $this->requirements[$k]['req'] = false;
        }
    }



//\1.1

//end smooth s...


//getters

    function get_charset() {
       if (!$this->wcomb_gmp) return NULL;
       return $this->wcomb_gmp->get_charsetstr(); 
    }


    function get_comb_size() {return $this->combcharnumb;} //int

    function get_comb_numb() {return $this->combnumb;} //int


//setters

    function set_charset($charsetstr="") {
        if (strlen($charsetstr) > 0) {
            $this->wcomb_gmp = new WCombin_GMP($charsetstr);
            filelog("set_chs BEFORE RETURN chs::" . $charsetstr . "::strlen::" . strlen($charsetstr));
            return;
        }

        filelog("set_CHS BEFORE SET 95 devpt_chs::" . $charsetstr . "::strlen::" . strlen($charsetstr));
        $this->wcomb_gmp = new WCombin_GMP($this->chsbuilder->get_charsetstr()); //this is a very subtle point!!

    }


    function set_comb_size($combcharnumb) {

        $this->combcharnumb = $combcharnumb; //int

    }

    function set_comb_numb($combnumb) {

        $this->combnumb = $combnumb; //int
    }
   

    function is_ready() {
        if (!$this->wcomb_gmp) return false;
        if (!$this->combcharnumb) return false;
	if (!$this->combnumb) return false;
	return true;
    }

    function check_correctness() { //call it somewhere, it's not in get_combinations()       
        $maxmin = $this->get_max_min();
	filelog("corrMAXMIN: \n");
	filelog(print_r($maxmin, true));
	filelog("GMP BROQ:" . gmp_add(gmp_sub($maxmin['combMaxNum'], $maxmin['combMinNum']), gmp_init(1
	)));
	filelog("GMP NASHIQ: " . gmp_init($this->combnumb));
	if ($this->get_uniqueness() && gmp_cmp(gmp_add(gmp_sub($maxmin['combMaxNum'], $maxmin['combMinNum']), gmp_init(1)), gmp_init($this->combnumb)) < 0) return -1; 

        if ($this->get_reqsort_number() > $this->combcharnumb) return -2;

	return true;
    }

    function get_max_min() { //assoc array
        
        $combMax = str_repeat(end($this->wcomb_gmp->charsetli), $this->combcharnumb);
        //max number, string, bijective number system 

        $combMin  = str_repeat($this->wcomb_gmp->charsetli[1], $this->combcharnumb);

        $combMaxNum = $this->wcomb_gmp->str2number($combMax); //gmp(!)
        //"real" max number 

        $combMinNum = $this->wcomb_gmp->str2number($combMin); //gmp(!)
        //"real" min number (for comb size)

        return array('combMax'=>$combMax, 'combMin'=>$combMin, 'combMaxNum'=>$combMaxNum, 'combMinNum'=>$combMinNum); //2strring,2gmp(!)


    }

    function get_combinations($ids = []) { //returns array 
//1.1 ids are optional ids (serials) if we want hard dependence (not randomness)
 
        echo "DEBUG: Start of get_combinations...\n";

        $combinations = array();

        if (!$this->wcomb_gmp) throw new Exception("wcomb_gmp member not configured!");
        if (!$this->combcharnumb) throw new Exception("number of characters not defined!");
        if (!$this->combnumb) throw new Exception("number of combinations not defined!");

        //1.1

        //phpAlert("AAAA: " . $this->chsbuilder->is_produce_case());
        if ($this->chsbuilder->is_produce_case()) {

            return $this->outer_funcs['produced_combinations']($this, $ids);

        }

        //\1.1

        $maxmin = $this->get_max_min();

        echo "debug: combMin / combMax: ${maxmin['combMin']} / ${maxmin['combMax']}\n";
        $stupiddebug2 = "debug: combMinNum / combMaxNum / all possible combs: ${maxmin['combMinNum']} / ${maxmin['combMaxNum']} / " . gmp_add(gmp_sub($maxmin['combMaxNum'], $maxmin['combMinNum']), gmp_init(1)) . "\n";

        if (php_sapi_name() === 'cli') echo $stupiddebug2;
	else echo htmlentities($stupiddebug2);

//gmp_random_seed - if you want a specific combination set (set a seed)



        for ($i = 1; $i <= $this->combnumb; $i++) {
            
            while (true) {
                while (true) {  
                    foreach ($this->requirements as $k=>$ar) $this->requirements[$k]['res'] = false; //next comb - clean 
                    $cur_combin = $this->wcomb_gmp->number2str($this->my_gmp_random_range2($maxmin['combMinNum'], $maxmin['combMaxNum']));
                    if ($this->check_requirements($cur_combin)) break; //second while
	        }
               
                if (!$this->get_uniqueness()) break; //first while
		elseif (!in_array($cur_combin, $combinations)) break; //first while
	    }

            array_push($combinations, [$cur_combin]); //an array because of 1.1
        } 
                    

        return $combinations;

    }

    //1.1
    function wcomb_gmp_number2str($gmp_integer) { return $this->wcomb_gmp->number2str($gmp_integer);}
    //\1.1


//it is CS.


    function gmp_log_b2($gmp_num) { //takes GMP, rerurns float
    
        //from itsid's function - SOf
    
        $string = gmp_strval($gmp_num);
    
        $intpart = strlen($string) - 1; //taking it from the form X*10**intpart
    
        $string = substr_replace($string, ".", 1, 0); //giving the X the float form - 'x.yzw...'
    
        $fl_string = floatval($string); //"real" float (losing some precision anyway) 
    
        $lg = $intpart + log10($fl_string); //using the formula log(x*y) = log(x) + log(y)
    
        $lb = $lg / log10(2); //using the formula log_a(x) = log_b(x) / log_b(a)
    
        return $lb; //it's float (here preserved itsid's var names)
    
    }

    function my_gmp_random_range1($gmp_min, $gmp_max) {

        $gmp_all_combs = gmp_add(gmp_sub($gmp_max, $gmp_min), gmp_init(1));

        $my_log_b2 = $this->gmp_log_b2(gmp_add($gmp_all_combs, gmp_init(1)));

        $BYTES_R = ceil($my_log_b2/8); //float

	$iBYTES_R = intval($BYTES_R);

	//filelog("BYTES FOR RANDOM_BYTES: ". intval($BYTES_R) . "\n"); //fills the log too much

        $for_cs_seed16 = strval(bin2hex(random_bytes($iBYTES_R)));

        gmp_random_seed(gmp_init($for_cs_seed16, 16));

        return gmp_random_range($gmp_min, $gmp_max);

    }


    function my_gmp_random_range2($gmp_min, $gmp_max) { //it uses random_bytes directly

        //my variant of Slava Fomin II's version of Skott's function on SOf

        $range = gmp_sub($gmp_max, $gmp_min); //(!)

        $bytes = (int) ceil($this->gmp_log_b2($range)/8);
	//filelog("BYTES FOR RANDOM_BYTES: $bytes \n");

        $bits = strlen(gmp_strval($range, 2));

        $filter = gmp_init(str_repeat('1', $bits), 2);

	//$tries = 0;

        do {
            $rnd = gmp_init(bin2hex(random_bytes($bytes)), 16);

            // Discard irrelevant bits.
            $rnd = gmp_and($rnd, $filter);
            //$tries++;

        } while (gmp_cmp($rnd, $range) > 0); //== is ok - $ $min + $range = $max

        //if ($tries > 1) filelog("RANDOM_BYTES REPETITION TRIES: ". $tries."\n");

        return gmp_add($gmp_min, $rnd);

    }



}

//CombGenerator_GMP



?>
