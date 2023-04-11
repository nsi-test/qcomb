<?php

$QCOMB_MAIN = "maindom3.php";

$QCOMB_SELF = str_replace('index.php', $QCOMB_MAIN, $_SERVER['PHP_SELF']);

echo '<script type="text/javascript">window.location.replace("' . "${_SERVER['REQUEST_SCHEME']}://${_SERVER['HTTP_HOST']}${QCOMB_SELF}?form=generate" .  '")</script>';


?>
