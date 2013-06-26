<?php
require("sonos.class.php");

$sonos = new SonosController('livingroom');

$volume = 0;
$force_unmute = 0;
 
if (isset($_GET['force_unmute'])) $force_unmute = $_GET['force_unmute']; // Force la désactivation de la sourdine. Optionnel
if (isset($_GET['volume'])) $volume = $_GET['volume']; // Niveau sonore. Optionnel.
$message = $_GET['message']; // Message à diffuser
 
//Instanciation de la classe
$sonos->PlayTTS($message, getcwd(), $volume, $force_unmute); //Lecture du message
