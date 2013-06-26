<?php
// Exemple d'application de la classe PHP Sonos permettant de baisser le niveau sonore s'il est supérieur à 50%

require("sonos.class.php");
//Instanciation de la classe
$sonos_1 = new SonosController('livingroom'); 
$volume = $sonos_1->GetVolume();
if ($volume > 50)
     $sonos_1 = $sonos_1->SetVolume(50);
