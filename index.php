<?php

$QCOMB_MAIN = "maindom3.php";

$QCOMB_SELF = str_replace('index.php', $QCOMB_MAIN, $_SERVER['PHP_SELF']);


$req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

echo '<script type="text/javascript">window.location.replace("' . "$req_scheme://${_SERVER['HTTP_HOST']}${QCOMB_SELF}?form=generate" .  '")</script>';


?>
