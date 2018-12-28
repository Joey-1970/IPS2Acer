<?
class IPS2AcerP5530 extends IPSModule
{
	public function Destroy() 
	{
		//Never delete this line!
		parent::Destroy();
		$this->SetTimerInterval("State", 0);
	}
	
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
		$this->RegisterPropertyBoolean("Open", false);
	    	$this->RegisterPropertyString("IPAddress", "127.0.0.1");
		$this->RegisterPropertyString("MAC", "00:00:00:00:00:00");
		$this->RegisterPropertyString("User", "User");
	    	$this->RegisterPropertyString("Password", "Passwort");
		$this->RegisterTimer("State", 0, 'IPS2AcerP5530_GetcURLData($_IPS["TARGET"]);');
		
		// Profile anlegen
		$this->RegisterProfileInteger("IPS2AcerP5530.Source", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 3, "HDMI 1", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 6, "HDMI 2/MHL", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 20, "VGA IN 1", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 21, "VGA IN 2", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 22, "Video", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 33, "LAN/Wifi", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 34, "Media", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 35, "USB Display", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 36, "Mirroring Display", "TV", -1);
		
		$this->RegisterProfileInteger("IPS2AcerP5530.DisplayMode", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.DisplayMode", 0, "Bright", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.DisplayMode", 1, "Presentation", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.DisplayMode", 2, "Standard", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.DisplayMode", 3, "Video", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.DisplayMode", 20, "User", "TV", -1);
		
		$this->RegisterProfileInteger("IPS2AcerP5530.AspectRatio", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.AspectRatio", 0, "Auto", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.AspectRatio", 1, "Full", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.AspectRatio", 2, "4:3", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.AspectRatio", 3, "16:9", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.AspectRatio", 20, "L.Box", "TV", -1);
		
		$this->RegisterProfileInteger("IPS2AcerP5530.Projection", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Projection", 0, "Front Table", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Projection", 1, "Rear Table", "TV", -1);
		
		
		$this->RegisterProfileInteger("IPS2AcerP5530.Volume", "Speaker", "", "", 0, 100, 5);

		// Statusvariablen anlegen
		$this->RegisterVariableInteger("LastKeepAlive", "Letztes Keep Alive", "~UnixTimestamp", 10);
		$this->DisableAction("LastKeepAlive");
		
		$this->RegisterVariableBoolean("Power", "Power", "~Switch", 50);
		$this->EnableAction("Power");
		
		$this->RegisterVariableBoolean("Hide", "Hide", "~Switch", 60);
		$this->EnableAction("Hide");
		
		$this->RegisterVariableBoolean("Freeze", "Freeze", "~Switch", 70);
		$this->EnableAction("Freeze");
		
		$this->RegisterVariableBoolean("ECO", "ECO", "~Switch", 80);
		$this->EnableAction("ECO");
		
		$this->RegisterVariableInteger("Source", "Source", "IPS2AcerP5530.Source", 90);
		$this->EnableAction("Source");
		
		$this->RegisterVariableInteger("Volume", "Volume", "IPS2AcerP5530.Volume", 100);
		$this->EnableAction("Volume");
		
		$this->RegisterVariableInteger("Brightness", "Brightness", "", 110);
		$this->EnableAction("Brightness");
		
		$this->RegisterVariableInteger("Contrast", "Contrast", "", 120);
		$this->EnableAction("Contrast");
		
		$this->RegisterVariableInteger("VKeystone", "V.-Keystone", "", 130);
		$this->EnableAction("VKeystone");
		
		$this->RegisterVariableInteger("HKeystone", "H.-Keystone", "", 140);
		$this->EnableAction("HKeystone");
	}
	
	public function GetConfigurationForm() { 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
		$arrayStatus[] = array("code" => 200, "icon" => "error", "caption" => "Instanz ist fehlerhaft"); 
		$arrayStatus[] = array("code" => 202, "icon" => "error", "caption" => "Kommunikationfehler!");
		
		$arrayElements = array(); 
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv"); 
		$arrayElements[] = array("type" => "ValidationTextBox", "name" => "IPAddress", "caption" => "IP");
		$arrayElements[] = array("type" => "ValidationTextBox", "name" => "MAC", "caption" => "MAC");
		$arrayElements[] = array("type" => "Label", "label" => "Zugriffsdaten der Projektor-Website");
		$arrayElements[] = array("type" => "ValidationTextBox", "name" => "User", "caption" => "User");
		$arrayElements[] = array("type" => "PasswordTextBox", "name" => "Password", "caption" => "Password");
 		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
		
		
		
		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	} 
	
	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();
		
		If (IPS_GetKernelRunlevel() == 10103) {
			If ($this->ReadPropertyBoolean("Open") == true) {
				
				$this->SetStatus(102);
				If ($this->ConnectionTest() == true) {
					// Erste Abfrage der Daten
					$this->GetData();
				}
				$this->SetTimerInterval("State", 5 * 1000);
				
			}
			else {
				$this->SetStatus(104);
				$this->SetTimerInterval("State", 0);
			}	   
		}
	}

	
	public function RequestAction($Ident, $Value) 
	{
  		If (($Ident == "Power") AND ($Value == true)) {
			$this->WakeOnLAN();
		}
		elseIf (($this->ReadPropertyBoolean("Open") == true) AND ($this->ConnectionTest() == true)) {
			switch($Ident) {
				case "Power":
					If ($Value == true) {
						$this->WakeOnLAN();
					}
					else {
						$this->SetcURLData("pwr=pwr");
					}
					break;
				case "ECO":
						$this->SetcURLData("eco=eco");
					break;
				case "Hide":
						$this->SetcURLData("hid=hid");
					break;
				case "Freeze":
						$this->SetcURLData("frz=frz");
					break;
				case "Source":
						$this->SetcURLData("src=".$Value);
					break;
				case "Volume":
						$this->SetcURLData("vol=".$Value);
					break;
				case "Brightness":
						$this->SetcURLData("bri=".$Value);
					break;
				case "Contrast":
						$this->SetcURLData("con=".$Value);
					break;
				case "VKeystone":
						$this->SetcURLData("vks=".$Value);
					break;
				case "HKeystone":
						$this->SetcURLData("hks=".$Value);
					break;
				
			default:
				    throw new Exception("Invalid Ident");
			}
	    	}
		
	}
	
	private function WakeOnLAN()
	{
    		$mac = $this->ReadPropertyString("MAC");
		$broadcast = "255.255.255.255";
		$mac_array = preg_split('#:#', $mac);
    		$hwaddr = '';

    		foreach($mac_array AS $octet)
    		{
        		$hwaddr .= chr(hexdec($octet));
    		}

    		// Create Magic Packet
    		$packet = '';
    		for ($i = 1; $i <= 6; $i++)
    		{
        		$packet .= chr(255);
    		}

    		for ($i = 1; $i <= 16; $i++)
    		{
        		$packet .= $hwaddr;
    		}

    		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    		if ($sock)
    		{
        		$options = socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, true);

        		if ($options >=0) 
        		{    
            			$e = socket_sendto($sock, $packet, strlen($packet), 0, $broadcast, 7);
            			socket_close($sock);
        		}    
    		}
	}
	
	private function SetcURLData(String $Message)
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetcURLData", "Message: ".$Message, 0);
			
			$User = $this->ReadPropertyString("User");;
			$Password = $this->ReadPropertyString("Password");
			$IPAddress = $this->ReadPropertyString("IPAddress");
			$URL = "http://".$IPAddress."/form/control_cgi";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
				"Accept: */*",
				"Accept-Language: en-us,en;q=0.5",
				"Accept-Encoding: gzip, deflate",
				"Connection: keep-alive",
					"Content-type: application/x-www-form-urlencoded",
			));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_USERPWD, "$User:$Password");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Message);
			$Response = curl_exec($ch);
			/*
			if(!curl_exec($ch)){
			    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
			}
			*/
			curl_close($ch);
			If ($Response <> Null) {
				$this->SetVariables($Response);
			}
			else {
				SetValueBoolean($this->GetIDForIdent("Power"), false);
				
				// restliche Statusvariablen disablen!
			}
			
		}
	}
	
	private function GetData()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetData", "Ausfuehrung", 0);
			$this->GetcURLData();
			// weitere Daten abholen
		}
	}
	
	public function GetcURLData()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetcURLData", "Ausfuehrung", 0);
			
			$User = $this->ReadPropertyString("User");;
			$Password = $this->ReadPropertyString("Password");
			$IPAddress = $this->ReadPropertyString("IPAddress");
			$URL = "http://".$IPAddress."/form/control_cgi";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "$User:$Password");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$Response = curl_exec($ch);
			curl_close($ch);
			
			If ($Response <> Null) {
				$this->SetVariables($Response);
			}
			else {
				SetValueBoolean($this->GetIDForIdent("Power"), false);
				
				// restliche Statusvariablen disablen!
			}
			
			//print_r($json);
			/*
			(
			    
			    
			    [gam] => 2.2 Gamma
			    [ctp] => CT1 Color Temp
			    [mod] => 255 Display Mode
			    [apr] => 255 Aspect Ratio
			    [zom] => 1.0 Digital Zoom
			    [prj] => 0 Projection
			    [lgo] => 0 Startup Screen
			    [aks] => 1 Auto Keystone 	
			    [dyar] => 29
			)
			*/

			//echo $json->pwr;
		}
	}
	
	private function SetVariables($Message)
	{
		// Anführungszeichen der Keys ergänzen
		$Response = preg_replace('/("(.*?)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?"|.)/s', '"$2$3"$4$6', $Message);
		// HTML-Tags entfernen
		$Response = strip_tags($Response);
		$Data = json_decode($Response);

		If ($Response <> Null) {
			SetValueInteger($this->GetIDForIdent("LastKeepAlive"), time() );

			If (GetValueBoolean($this->GetIDForIdent("Power")) <> boolval($Data->pwr)) {
				SetValueBoolean($this->GetIDForIdent("Power"), boolval($Data->pwr));
			}
			If (GetValueBoolean($this->GetIDForIdent("Freeze")) <> boolval($Data->frz)) {
				SetValueBoolean($this->GetIDForIdent("Freeze"), boolval($Data->frz));
			}
			If (GetValueBoolean($this->GetIDForIdent("Hide")) <> boolval($Data->hid)) {
				SetValueBoolean($this->GetIDForIdent("Hide"), boolval($Data->hid));
			}
			If (GetValueBoolean($this->GetIDForIdent("ECO")) <> boolval($Data->eco)) {
				SetValueBoolean($this->GetIDForIdent("ECO"), boolval($Data->eco));
			}
			If (GetValueInteger($this->GetIDForIdent("Source")) <> intval($Data->src)) {
				SetValueInteger($this->GetIDForIdent("Source"), intval($Data->src));
			}
			If (GetValueInteger($this->GetIDForIdent("Volume")) <> intval($Data->vol)) {
				SetValueInteger($this->GetIDForIdent("Volume"), intval($Data->vol));
			}
			If (GetValueInteger($this->GetIDForIdent("Brightness")) <> intval($Data->bri)) {
				SetValueInteger($this->GetIDForIdent("Brightness"), intval($Data->bri));
			}
			If (GetValueInteger($this->GetIDForIdent("Contrast")) <> intval($Data->con)) {
				SetValueInteger($this->GetIDForIdent("Contrast"), intval($Data->con));
			}
			If (GetValueInteger($this->GetIDForIdent("VKeystone")) <> intval($Data->vks)) {
				SetValueInteger($this->GetIDForIdent("VKeystone"), intval($Data->vks));
			}
			If (GetValueInteger($this->GetIDForIdent("HKeystone")) <> intval($Data->hks)) {
				SetValueInteger($this->GetIDForIdent("HKeystone"), intval($Data->hks));
			}
		}
		else {
			SetValueBoolean($this->GetIDForIdent("Power"), false);

			// restliche Statusvariablen disablen!
		}
	}
	
	private function ConnectionTest()
	{
	      $result = false;
	      If (Sys_Ping($this->ReadPropertyString("IPAddress"), 2000)) {
			//IPS_LogMessage("IPS2AcerP5530","Angegebene IP ".$this->ReadPropertyString("IPAddress")." reagiert");
			$this->SetStatus(102);
		      	$result = true;
		}
		else {
			IPS_LogMessage("IPS2AcerP5530","IP ".$this->ReadPropertyString("IPAddress")." reagiert nicht!");
			$this->SendDebug("ConnectionTest", "IP ".$this->ReadPropertyString("IPAddress")." reagiert nicht!", 0);
			$this->SetStatus(104);
		}
	return $result;
	}
	
	private function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
	{
	        if (!IPS_VariableProfileExists($Name))
	        {
	            IPS_CreateVariableProfile($Name, 1);
	        }
	        else
	        {
	            $profile = IPS_GetVariableProfile($Name);
	            if ($profile['ProfileType'] != 1)
	                throw new Exception("Variable profile type does not match for profile " . $Name);
	        }
	        IPS_SetVariableProfileIcon($Name, $Icon);
	        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
	        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);    
	}    
	
	private function RegisterProfileFloat($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
	{
	        if (!IPS_VariableProfileExists($Name))
	        {
	            IPS_CreateVariableProfile($Name, 2);
	        }
	        else
	        {
	            $profile = IPS_GetVariableProfile($Name);
	            if ($profile['ProfileType'] != 2)
	                throw new Exception("Variable profile type does not match for profile " . $Name);
	        }
	        IPS_SetVariableProfileIcon($Name, $Icon);
	        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
	        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
	        IPS_SetVariableProfileDigits($Name, $Digits);
	}
}
?>
