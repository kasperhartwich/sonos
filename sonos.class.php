<?php
/**
  * PHP class to control Sonos
  * http://www.github.com/DjMomo/sonos for updates
  * 
  * Available functions :
  * - Play() : play / lecture 
  * - Pause() : pause
  * - Stop() : stop
  * - Next() : next track / titre suivant 
  * - Previous() : previous track / titre précédent
  * - SeekTime(string) : seek to time xx:xx:xx / avancer-reculer à la position xx:xx:xx
  * - ChangeTrack(int) : change to track xx / aller au titre xx
  * - RestartTrack() : restart actual track / revenir au début du titre actuel
  * - RestartQueue() : restart queue / revenir au début de la liste actuelle
  * - GetVolume() : get volume level / récupérer le niveau sonore actuel
  * - SetVolume(int) : set volume level / régler le niveau sonore
  * - GetMute() : get mute status / connaitre l'état de la sourdine
  * - SetMute(bool) : active-disable mute / activer-désactiver la sourdine
  * - GetTransportInfo() : get status about player / connaitre l'état de la lecture
  * - GetMediaInfo() : get informations about media / connaitre des informations sur le média
  * - GetPositionInfo() : get some informations about track / connaitre des informations sur le titre
  * - AddURIToQueue(string,bool) : add a track to queue / ajouter un titre à la liste de lecture
  * - RemoveTrackFromQueue(int) : remove a track from Queue / supprimer un tritre de la liste de lecture
  * - RemoveAllTracksFromQueue() : remove all tracks from queue / vider la liste de lecture
  * - RefreshShareIndex() : refresh music library / rafraichit la bibliothèque musicale
  * - SetQueue(string) : load a track or radio in player / charge un titre ou une radio dans le lecteur
  * - PlayTTS(string message,string station,int volume,string lang) : play a text-to-speech message / lit un message texte
*/
class SonosController
{
    private $ip;
    private $port;
    private $language;
    private $local_tts_dir;
    private $shared_tts_dir;
    
    /**
    * Constructeur
    * @param string Device specified in config file
    */
    public function __construct($device)
    {
        //Load ini file.
        if (is_file('config.ini')) {
            $ini = parse_ini_file('config.ini', true);            
        } else {
            if (is_file('../config.ini')) {
                $ini = parse_ini_file('../config.ini', true);            
            } else {
                exit('No configuration file found.');
            }
        }

        //Find device
        if (!isset($ini[$device])) { exit('Unknown device.'); }
        $this->ip = $ini[$device]['ip'];
        $this->port = isset($ini[$device]['port']) ? $ini[$device]['port'] : $ini['port'];
        $this->language = isset($ini[$device]['language']) ? $ini[$device]['language'] : $ini['language'];
        $this->local_tts_dir = isset($ini[$device]['local_tts_dir']) ? $ini[$device]['local_tts_dir'] : $ini['local_tts_dir'];
        $this->shared_tts_dir = isset($ini[$device]['shared_tts_dir']) ? $ini[$device]['shared_tts_dir'] : $ini['shared_tts_dir'];
    }
  
    private function Upnp($url,$SOAP_service,$SOAP_action,$SOAP_arguments = '',$XML_filter = '')
    {
        $POST_xml = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
        $POST_xml .= '<s:Body>';
        $POST_xml .= '<u:' . $SOAP_action . ' xmlns:u="'.$SOAP_service.'">';
        $POST_xml .= $SOAP_arguments;
        $POST_xml .= '</u:'.$SOAP_action.'>';
        $POST_xml .= '</s:Body>';
        $POST_xml .= '</s:Envelope>';

        $POST_url = $this->ip . ":" . $this->port . $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $POST_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "SOAPAction: ".$SOAP_service."#".$SOAP_action));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POST_xml);
        $r = curl_exec($ch);
        curl_close($ch);

        if ($XML_filter != '')
            return $this->Filter($r,$XML_filter);
        else
            return $r;
    }
  
    private function Filter($subject,$pattern)
    {
        preg_match('/\<'.$pattern.'\>(.+)\<\/'.$pattern.'\>/',$subject,$matches); ///'/\<'.$pattern.'\>(.+)\<\/'.$pattern.'\>/'
        return isset($matches[1]) ? $matches[1] : '';
    }
  
    /**
    * Play
    */
    public function Play()
    {
    
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Play';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID><Speed>1</Speed>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Pause
    */
    public function Pause()
    {
    
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Pause';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Stop
    */
    public function Stop()
    {
    
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Stop';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Next
    */
    public function Next()
    {
    
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Next';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Previous
    */
    public function Previous()
    {
    
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Previous';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        return $this->Upnp($url,$service,$action,$args);
    }
  
    /**
    * Seek to position xx:xx:xx or track number x
    * @param string 'REL_TIME' for time position (xx:xx:xx) or 'TRACK_NR' for track in actual queue
    * @param string 
    */
    public function Seek($type,$position)
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'Seek';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID><Unit>'.$type.'</Unit><Target>'.$position.'</Target>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Seek to time xx:xx:xx
    */
    public function SeekTime($time)
    {
        return $this->Seek("REL_TIME",$time);
    }
    
    /**
    * Change to track number
    */
    public function ChangeTrack($number)
    {
        return $this->Seek("TRACK_NR",$number);
    }
    
    /**
    * Restart actual track
    */
    public function RestartTrack()
    {
        return $this->Seek("REL_TIME","00:00:00");
    }
     
    /**
    * Restart actual queue
    */
    public function RestartQueue()
    {
        return $this->Seek("TRACK_NR","1");
    }
         
    /**
    * Get volume value (0-100)
    */
    public function GetVolume()
    {
    
        $url = '/MediaRenderer/RenderingControl/Control';
        $action = 'GetVolume';
        $service = 'urn:schemas-upnp-org:service:RenderingControl:1';
        $args = '<InstanceID>0</InstanceID><Channel>Master</Channel>';
        $filter = 'CurrentVolume';
        return $this->Upnp($url,$service,$action,$args,$filter);
    }
    
    /**
    * Set volume value (0-100)
    */
    public function SetVolume($volume)
    {
    
        $url = '/MediaRenderer/RenderingControl/Control';
        $action = 'SetVolume';
        $service = 'urn:schemas-upnp-org:service:RenderingControl:1';
        $args = '<InstanceID>0</InstanceID><Channel>Master</Channel><DesiredVolume>'.$volume.'</DesiredVolume>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Get mute status
    */
    public function GetMute()
    {
        $url = '/MediaRenderer/RenderingControl/Control';
        $action = 'GetMute';
        $service = 'urn:schemas-upnp-org:service:RenderingControl:1';
        $args = '<InstanceID>0</InstanceID><Channel>Master</Channel>';
        $filter = 'CurrentMute';
        return $this->Upnp($url,$service,$action,$args,$filter);
    }
    
    /**
    * Set mute
    * @param integer mute active=1
    */
    public function SetMute($mute = 0)
    {
        $url = '/MediaRenderer/RenderingControl/Control';
        $action = 'SetMute';
        $service = 'urn:schemas-upnp-org:service:RenderingControl:1';
        $args = '<InstanceID>0</InstanceID><Channel>Master</Channel><DesiredMute>'.$mute.'</DesiredMute>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Get Transport Info : get status about player
    */
    public function GetTransportInfo()
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'GetTransportInfo';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        $filter = 'CurrentTransportState';
        return $this->Upnp($url,$service,$action,$args,$filter);
    }
    
    /**
    * Get Media Info : get informations about media
    */
    public function GetMediaInfo()
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'GetMediaInfo';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        $filter = 'CurrentURI';
        return $this->Upnp($url,$service,$action,$args,$filter);
    }
    
    /**
    * Get Position Info : get some informations about track
    */
    public function GetPositionInfo()
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'GetPositionInfo';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        $xml = $this->Upnp($url,$service,$action,$args);
        
        $data["TrackNumberInQueue"] = $this->Filter($xml,"Track");
        $data["TrackURI"] = $this->Filter($xml,"TrackURI");
        $data["TrackDuration"] = $this->Filter($xml,"TrackDuration");
        $data["RelTime"] = $this->Filter($xml,"RelTime");
        $TrackMetaData = $this->Filter($xml,"TrackMetaData");
        
        $xml = substr($xml, stripos($TrackMetaData, '&lt;'));
        $xml = substr($xml, 0, strrpos($xml, '&gt;') + 4);
        $xml = str_replace(array("&lt;", "&gt;", "&quot;", "&amp;", "%3a", "%2f", "%25"), array("<", ">", "\"", "&", ":", "/", "%"), $xml);
        
        $data["Title"] = $this->Filter($xml,"dc:title");    // Track Title
        $data["AlbumArtist"] = $this->Filter($xml,"r:albumArtist");        // Album Artist
        $data["Album"] = $this->Filter($xml,"upnp:album");        // Album Title
        $data["TitleArtist"] = $this->Filter($xml,"dc:creator");    // Track Artist
        
        return $data;
    }
    
    /**
    * Add URI to Queue
    * @param string track/radio URI
    * @param bool added next (=1) or end queue (=0) 
    */
    public function AddURIToQueue($URI,$next=0)
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'AddURIToQueue';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID><EnqueuedURI>'.$URI.'</EnqueuedURI><EnqueuedURIMetaData></EnqueuedURIMetaData><DesiredFirstTrackNumberEnqueued>0</DesiredFirstTrackNumberEnqueued><EnqueueAsNext>1</EnqueueAsNext>';
        $filter = 'FirstTrackNumberEnqueued';
        return $this->Upnp($url,$service,$action,$args,$filter);
    }
    
    /**
    * Remove a track from Queue
    *
    */
    public function RemoveTrackFromQueue($tracknumber)
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'RemoveTrackFromQueue';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID><ObjectID>Q:0/'.$tracknumber.'</ObjectID>';
        return $this->Upnp($url,$service,$action,$args);
    }
    
    /**
    * Clear Queue
    *
    */
    public function RemoveAllTracksFromQueue()
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'RemoveAllTracksFromQueue';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID>';
        return $this->Upnp($url,$service,$action,$args);
    }

    /**
    * Set Queue
    * @param string URI of new track
    */
    public function SetQueue($URI)
    {
        $url = '/MediaRenderer/AVTransport/Control';
        $action = 'SetAVTransportURI';
        $service = 'urn:schemas-upnp-org:service:AVTransport:1';
        $args = '<InstanceID>0</InstanceID><CurrentURI>'.$URI.'</CurrentURI><CurrentURIMetaData></CurrentURIMetaData>';
        return $this->Upnp($url,$service,$action,$args);
    }
        
    /**
    * Refresh music library
    *
    */
    public function RefreshShareIndex()
    {
        $url = '/MediaServer/ContentDirectory/Control';
        $action = 'RefreshShareIndex';
        $service = 'urn:schemas-upnp-org:service:ContentDirectory:1';
        return $this->Upnp($url,$service,$action,$args);
    }

    /**
    * Split string in several strings
    *
    */
    private function CutString($string,$intmax)
    {
        $i = 0;
        while (strlen($string) > $intmax)
        {
            $string_cut = substr($string, 0, $intmax);
            $last_space = strrpos($string_cut, "+");
            $strings[$i] = substr($string, 0, $last_space);
            $string = substr($string, $last_space, strlen($string));
            $i++;
        }
        $strings[$i] = $string;
        return $strings;
    }
        
    /**
    * Convert Words (text) to Speech (MP3)
    *
    */
    public function TTSToMp3($words, $language = false)
    {
        if (!$language) {$language = $this-language;}
        // Directory
        $folder = $this->local_tts_dir . '/' . $language;

        // If folder doesn't exists, create it
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        
        // Replace the non-alphanumeric characters
        // The spaces in the sentence are replaced with the Plus symbol
        $words = urlencode($words);
 
        // Name of the MP3 file generated using the MD5 hash
        $filename = "TTS-" . md5($words) . ".mp3";
  
        // Save the MP3 file in this folder with the .mp3 extension
        $file = $folder . '/' . $filename;

        // If the MP3 file exists, do not create a new request
        if (!file_exists($file)) 
        {
            // Google Translate API cannot handle strings > 100 characters
            $words = $this->CutString($words,100);
        
            ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
            $mp3 = "";
            for ($i = 0; $i < count($words); $i++)
                $mp3[$i] = file_get_contents('http://translate.google.com/translate_tts?q=' . $words[$i] . '&tl=' . $language);
            
            file_put_contents($file, $mp3);
        }
        return $filename;
    }
    
    /**
    * Say song name via TTS message
    * @param string message
    * @param string radio name display on sonos controller
    * @param int volume
    * @param string language
    */
    public function SongNameTTS($volume=0,$unmute=0,$lang='fr')
    {
        $ThisSong = "Cette chanson s'appelle ";
        $By = " de ";
      
        $actual['track'] = $this->GetPositionInfo();

        $SongName = $actual['track']['Title'];
        $Artist = $actual['track']['TitleArtist'];

        $message = $ThisSong . $SongName . $By . $Artist ;
      
        $this->PlayTTS($message,$volume,$unmute,$lang);
      
        return true;
    }
    
    /**
    * Play a TTS message
    * @param string message
    * @param string language
    */
    public function PlayTTS($message, $language = false, $unmute = 1, $volume = false)
    {
        if (!$language) {$language = $this->language;}
        $actual['track'] = $this->GetPositionInfo();
        $actual['volume'] = $this->GetVolume();      
        $actual['mute'] = $this->GetMute();       
        $actual['status'] = $this->GetTransportInfo();
        $this->Pause();
            
        if ($unmute == 1)
            $this->SetMute(0);
        if ($volume)
            $this->SetVolume($volume);

        $file = 'x-file-cifs:' . $this->shared_tts_dir . '/' . $language . '/' . $this->TTSToMp3($message, $language);
        if (((stripos($actual['track']["TrackURI"],"x-file-cifs://")) != false) or ((stripos($actual['track']["TrackURI"],".mp3")) != false))
        {
            // It's a MP3 file
            $TrackNumber = $this->AddURIToQueue($file);
            $this->ChangeTrack($TrackNumber);
            $this->Play();
            sleep(2);
            while ($this->GetTransportInfo() == "PLAYING") {}
            $this->Pause();
            $this->SetVolume($actual['volume']);
            $this->SetMute($actual['mute']);
            $this->ChangeTrack($actual['track']["TrackNumberInQueue"]);
            $this->SeekTime($actual['track']["RelTime"]);
            $this->RemoveTrackFromQueue($TrackNumber);
        }
        else
        {
            //It's a radio / or TV (playbar) / or nothing
            $this->SetQueue($file);
            $this->Play();
            sleep(2);
            while ($this->GetTransportInfo() == "PLAYING") {}
            $this->Pause();
            $this->SetVolume($actual['volume']);
            $this->SetMute($actual['mute']);
            $this->SetQueue($actual['track']["TrackURI"]);
        }

        if (strcmp($actual['status'],"PLAYING") == 0)
            $this->Play();
        return true;
    }
    
    public function control($command, $parameter1 = false, $parameter2 = false) {
        /*
         * TODO:
         * - SeekTime(string) : seek to time xx:xx:xx / avancer-reculer à la position xx:xx:xx
         * - ChangeTrack(int) : change to track xx / aller au titre xx
         * - GetTransportInfo() : get status about player / connaitre l'état de la lecture
         * - GetMediaInfo() : get informations about media / connaitre des informations sur le média
         * - GetPositionInfo() : get some informations about track / connaitre des informations sur le titre
         * - AddURIToQueue(string,bool) : add a track to queue / ajouter un titre à la liste de lecture
         * - RemoveTrackFromQueue(int) : remove a track from Queue / supprimer un tritre de la liste de lecture
         * - RemoveAllTracksFromQueue() : remove all tracks from queue / vider la liste de lecture
         * - RefreshShareIndex() : refresh music library / rafraichit la bibliothèque musicale
         * - SetQueue(string) : load a track or radio in player / charge un titre ou une radio dans le lecteur
         * - PlayTTS(string message,string station,int volume,string lang) : play a text-to-speech message / lit un message texte
        **/
        switch ($command) {
            case 'play':
                return $this->Play();
                break;
            case 'pause':
                return $this->Pause();
                break;
            case 'stop':
                return $this->Stop();
                break;
            case 'next':
                return $this->Next();
                break;
            case 'previous':
                return $this->Previous();
                break;
            case 'restart':
                switch ($parameter1) { //Which 'restart' parameter:
                    case 'track':
                        return $this->RestartTrack();
                        exit;
                    case 'queue':
                        return $this->RestartQueue();
                        exit;
                    default:
                        return "Requires 'track' or 'queue' parameter.\n";
                        exit;
                }        
                break;
            case 'get':
                switch ($parameter1) { //Which 'get' parameter:
                    case 'volume':
                        return $this->GetVolume();
                        exit;
                    case 'mute':
                        return $this->GetMute();
                        exit;
                    case 'media':
                        return $this->GetMediaInfo();
                        exit;
                    case 'transport':
                        return $this->GetTransportInfo();
                        exit;
                    case 'position':
                        return $this->GetPositionInfo();
                        exit;
                    default:
                        return "Incorrect get parameter.\n";
                        exit;
                }
            case 'set':
                switch ($parameter1) { //Which 'get' parameter:
                    case 'volume':
                        if (!$parameter2) {exit("No volume level specified.\n");}
                        if ($parameter2=='up') {
                            $volume = $this->GetVolume();
                            $this->SetVolume($volume>100 ? 100 : $volume+2);
                        } else if ($parameter2=='down') {
                            $volume = $this->GetVolume();
                            $this->SetVolume($volume<1 ? 1 : $volume-2);
                        } else {
                            $this->SetVolume($parameter2);
                        }
                        return $this->GetVolume();
                        exit;
                    case 'mute':
                        $this->SetMute($parameter2=='on');
                        return $this->GetMute() ? "System is now muted\n" : "System is now unmuted\n";
                        exit;
                    default:
                        return "Incorrect set parameter.\n";
                        exit;
                }
            case 'queue':
                switch ($parameter1) { //Which 'get' parameter:
                    case 'reset':
                        $this->RemoveAllTracksFromQueue();
                        return "Queue has been reset\n";
                        exit;
                    default:
                        return "Incorrect queue parameter.\n";
                        exit;
                }

            case 'tts':
                return $this->PlayTTS($parameter1, $parameter2);
                break;
            default:
                return "Unknown command.";
        }
        
    }
}
