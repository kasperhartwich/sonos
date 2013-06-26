<?php
// Exemple d'application de la classe PHP Sonos permettant de baisser le niveau sonore s'il est supérieur à 50%

$IP_sonos_1 = "192.168.1.11"; // A adapter avec l'adresse IP du Sonos à contrôler

require("sonos.class.php");
//Instanciation de la classe
$sonos_1 = new SonosController($IP_sonos_1); 
$volume = $sonos_1->GetVolume();
if ($volume > 50)
     $sonos_1 = $sonos_1->SetVolume(50);
