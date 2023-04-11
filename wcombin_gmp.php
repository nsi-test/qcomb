<?php


class WCombin_GMP {

    protected $GMP_ZERO, $GMP_ONE;

    function __construct($charsetstr) {

        $this->GMP_ZERO = gmp_init(0);
        $this->GMP_ONE = gmp_init(1);
        
        $this->CHARSETSTR = $charsetstr; //space is allowed as a symbol
        $cand_charsetli = str_split($this->CHARSETSTR);
        if (count($cand_charsetli) > count(array_unique($cand_charsetli))) {
            throw new Exception("Duplicate characters in wcomb_gmp creation!"); //sorry, I think its better than die
        }
        $this->charsetli = $cand_charsetli;

        $this->radix = gmp_init(array_unshift($this->charsetli, NULL) -1); //we want indices from 1

        echo $this->radix."\n"; //this line can be deleted

     }


//"old" funcs

    function str2Num($word) {

       $wordli = str_split($word);
       $wordli = array_reverse($wordli); //reversed arrays are easier for number works
       foreach ($wordli as $char) {
           $resp[] = gmp_init(array_search($char, $this->charsetli));
       }  
       return $resp;
    }


    function num2Str($numlist) { //number list is reversed!!
        //php doesn't change arrays in place
        $numlist = array_reverse($numlist);
        foreach ($numlist as $N) {
                $reswordli[] = $this->charsetli[gmp_intval($N)]; 
        }
        return implode("", $reswordli);
    }


    function increm($numwordli, $pos) { //recursive

    if ($pos == count($numwordli)) { //has sense only in recursion
        array_push($numwordli, $this->GMP_ONE); //important point
        return $numwordli;
    }

    if (gmp_cmp($numwordli[$pos], $this->radix) === 0) {
        $numwordli[$pos] = $this->GMP_ONE;
        $numwordli = $this->increm($numwordli, $pos + 1);
        return $numwordli;
    } 

    $numwordli[$pos] = gmp_add($numwordli[$pos], $this->GMP_ONE);

    return $numwordli;

    //$pos is int
    }


    function wincrem($word) {
    
        $numlist = $this->str2Num($word);
        $numlist = $this->increm($numlist, 0);
        return $this->num2Str($numlist);
    
    }


//string to number and opposite

    function str2number($word) {

        $numli = $this->str2Num($word); //it is reversed!!
        $number = $this->GMP_ZERO;
        for ($ind = 0; $ind <= count($numli) - 1; $ind++) {
            $number = gmp_add($number, gmp_mul($numli[$ind], gmp_pow($this->radix, $ind))); //very subtle - already reversed
            //the well known formula (originally reversed $numli is correct)
            //$ind is int, gmp_pow requires it
        }

        return $number;

    }


    function number2str($number) {
        //$number has to be gmp
        $numlist = array();


        while (gmp_cmp($number, $this->GMP_ZERO) !== 0) {
            $dig = gmp_div_r($number, $this->radix);
            if (gmp_cmp($dig, $this->GMP_ZERO) !== 0) { //subtle point
                array_push($numlist, $dig);
                $number = gmp_div_q($number, $this->radix);
            }
            else {
                array_push($numlist, $this->radix);
                $number = gmp_sub(gmp_div_q($number, $this->radix), $this->GMP_ONE); //crucial!
            }
        }

        return $this->num2Str($numlist); //the function does the reverse work
    }


    function number2str_list($number) {

        return str_split($this->number2str($number));

    }


//getters

    function get_charsetstr() {return $this->CHARSETSTR;}

    function get_radix() {return $this->radix;} //this is gmp


} //WCombin_GMP    


?>
