<?php
/**
 * API Constroller for Sonos
 */
require("../sonos.class.php");

$sonos = new SonosController($_GET['device']);
$response = $sonos->control(
    $_GET['command'],
    isset($_GET['parameter1']) ? $_GET['parameter1'] : false,
    isset($_GET['parameter2']) ? $_GET['parameter2'] : false
);
echo $response;