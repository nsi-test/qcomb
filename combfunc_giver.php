<?php


class CFuncGiver {


    protected $gived_funcs; //assoc arr

    function __construct() {

        $this->gived_funcs = [];

        $this->gived_funcs['test'] =  [$this, 'testone'];

        $this->gived_funcs['produced_combinations'] =  [$this, 'get_produced_combinations'];

    }

    function give_func($fname) {

        return $this->gived_funcs[$fname];

    }

    function testone($param) { //param - string

        return "test" . $param;

    } //test

    function get_produced_combinations($CG, $ids) {

        echo "DEBUG: in the outer function get_produced_combinations \n";

        $combinations = [];

        //$combinations = array_merge($combinations, $ids);

        foreach ($ids as $id) {
            //echo $id[0] ."\n";

            $hashed_id = hash('sha256', $id[0]); //this is a 2 dim arr

            $gmp_hashed_id = gmp_init($hashed_id, 16);

            $comb_id = $CG->wcomb_gmp_number2str($gmp_hashed_id);

            if (strlen($comb_id) < $CG->get_comb_size()) throw new Exception("not enough characters in hash.");

            //$cur_prod_comb = substr($comb_id, -$CG->get_comb_size()); //deprecated
            $cur_prod_comb = substr($comb_id, 0, $CG->get_comb_size());

            array_push($combinations, [$id[0], $cur_prod_comb]); //$id is array

        }

        return $combinations;

    } //get_produced_combinations


} //CFuncGiver class




?>
