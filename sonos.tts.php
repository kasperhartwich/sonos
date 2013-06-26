<?php
if (!is_file('config.ini')) { exit('No configuration file found.');}
$ini = parse_ini_file('config.ini');

require("sonos.class.php");

$volume = 0;
$force_unmute = 0;
 
if (isset($_GET['force_unmute'])) $force_unmute = $_GET['force_unmute']; // Force la désactivation de la sourdine. Optionnel
if (isset($_GET['volume'])) $volume = $_GET['volume']; // Niveau sonore. Optionnel.
$message = $_GET['message']; // Message à diffuser
 
//Instanciation de la classe
$sonos_1 = new SonosPHPController($ini['ip']);
$sonos_1->PlayTTS($message, getcwd(), $volume, $force_unmute); //Lecture du message
