<?php

set_time_limit(30); //maximum execution time in seconds - just in case of a bug, to save the servers recource 
//not working with WSL

require_once "myhtmldom.php";

$htd = new MyHtmlDom();

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
} //thanks to maqs from stack overflow


function setVar($var, $val) {
    echo '<script type="text/javascript">var ' . $var . ' = ' . $val . ' </script>';
} //mine



require_once "myconfig.php";
try {
    $conf = new MyConfig('config.xml');
    $conf->read_or_create();
    $LOG = $conf->get_option('log_to_file');
    $LOG = intval($LOG);
}
catch (Exception $e) {
    echo "Error reading config file!";
}

if (!isset($LOG)) $LOG = false;

function fileLog($msg) {
    global $LOG;
    if (!$LOG) return;
    $logfile = fopen("qlog.txt", "a") or die("Unable to open file!");
    fwrite($logfile, date("Y-m-d H:i:s") . ": ");
    fwrite($logfile, $msg);
    fwrite($logfile, "\n");
    fclose($logfile);
}


//reset part
function reset_it() {
    //the whole cookie deletion part and session things in this function is Tony Stark's, SOf
    $_SESSION = array();

    //deleting cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
        );
    } //end del c.

    session_destroy();

    //mine - to start a new session, cookie, etc.
    echo '<script type="text/javascript">window.open("' . "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?form=generate" .  '", "_self")</script>';

}



//shut part, thanks to user259973, Manoj Sharma on SOf (very abbreviated)
function shutdown_handler() {

    $error = error_get_last();

    if($error === NULL || $error['type'] != E_ERROR) return;

    error_log("from qcomb - IN SHUTDOWN FUNC - FATAL ERR::" . print_r($error, true));

    print_r($error);
    phpAlert("Error type: ${error['type']} | Message: ${error['message']} | File: ${error['file']} | Line number: ${error['file']}");

    reset_it();

}

register_shutdown_function("shutdown_handler");
//shut

?>

