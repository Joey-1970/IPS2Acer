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
		$this->RegisterTimer("State", 0, 'IPS2AcerP5530_State($_IPS["TARGET"]);');
		
		// Profile anlegen
		$this->RegisterProfileInteger("IPS2AcerP5530.Source", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 3, "HDMI 1", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 6, "HDMI 2/MHL", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 20, "VGA IN 1", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 21, "VGA IN 2", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 22, "Video", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 33, "LAN/Wifi", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 34, "Media", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 35, "USB Display", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 36, "Mirroring Display", "TV", -1);
		
		

		// Statusvariablen anlegen
		$this->RegisterVariableInteger("LastKeepAlive", "Letztes Keep Alive", "~UnixTimestamp", 10);
		$this->DisableAction("LastKeepAlive");
		
		$this->RegisterVariableString("Name", "Name", "", 20);
		$this->RegisterVariableString("Model", "Model", "", 30);
		$this->RegisterVariableString("Res", "Resolution", "", 40);
		
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
			If (($this->ReadPropertyBoolean("Open") == true) AND ($this->ConnectionTest() == true)) {
				
				$this->SetStatus(102);
				// Erste Abfrage der Daten
				$this->GetData();
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
  		If (($this->ReadPropertyBoolean("Open") == true) AND ($this->GetParentStatus() == 102)) {
			switch($Ident) {
				case "Power":
					If ($Value == true) {
						$this->SetData("OKOKOKOKOK");
						$this->SetData("* 0 IR 001");
					}
					else {
						$this->SetData("* 0 IR 002");
					}
					break;
				case "ECO":
					If ($Value == true) {
						$this->SetData("* 0 IR 051");
					}
					else {
						$this->SetData("* 0 IR 055");
					}
					break;
				case "Control":
					SetValueInteger($this->GetIDForIdent("Control"), $Value);
					If ($Value == 0) {
						// Left
						$this->SetData("* 0 IR 012");
					}
					else If ($Value == 1) {
						// Up
						$this->SetData("* 0 IR 009");
					}
					else If ($Value == 2) {
						// Enter
						$this->SetData("* 0 IR 013");
					}
					else If ($Value == 3) {
						// Down
						$this->SetData("* 0 IR 010");
					}
					else If ($Value == 4) {
						// Down
						$this->SetData("* 0 IR 011");
					}
					break;
			default:
				    throw new Exception("Invalid Ident");
			}
	    	}
		
	}
	
	private function wol($broadcast, $mac)
	{
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
	
	public function State()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$Message = "* 0 Lamp ?".chr(13);
			$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
			If (GetValueBoolean($this->GetIDForIdent("Power")) == true) {
				$MessageArray = array("* 0 Src ?", "* 0 IR 052", "* 0 IR 073", "* 0 Lamp");
				foreach ($MessageArray as $Value) {
					$Message = $Value.chr(13);
					$this->SetBuffer("LastMessage", $Value);
					$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
					IPS_Sleep(300);
				}
			}
		}
	}
	
	private function SetData(String $Message)
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetData", "Message: ".$Message, 0);
			$this->SetBuffer("LastMessage", $Message);
			$Message = $Message.chr(13);
			$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
			IPS_Sleep(300);
		}
	}
	
	private function GetData()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetData", "Ausfuehrung", 0);
			$MessageArray = array("* 0 Lamp ?", "* 0 Src ?", "* 0 IR 035", "* 0 IR 036", "* 0 IR 037", "* 0 IR 052", "* 0 IR 073", "* 0 Lamp");
			foreach ($MessageArray as $Value) {
				$Message = $Value.chr(13);
				$this->SetBuffer("LastMessage", $Value);
				$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
				IPS_Sleep(300);
			}
		}
	}
	
	public function GetcURLData()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetcURLData", "Ausfuehrung", 0);
			$username = "Administrator";
			$password = "admin";
			$url = "http://192.168.178.21/form/control_cgi";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$output = curl_exec($ch);
			curl_close($ch);
			
			
			$output = preg_replace('/("(.*?)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?"|.)/s', '"$2$3"$4$6', $output);
			$output = strip_tags($output);
			$json = json_decode($output);
			//print_r($json);
			/*
			(
			    [pwr] => 1  Power Bool
			    [hid] => 0 Hide Bool
			    [frz] => 0 Freeze
			    [eco] => 0 ECO
			    [src] => 6 Source
			    [bri] => 0 Brightness
			    [con] => 0 Contrast
			    [vks] => 0 V. Keystone
			    [hks] => 0 H. Keystone
			    [gam] => 2.2 Gamma
			    [ctp] => CT1 Color Temp
			    [mod] => 255 Display Mode
			    [vol] => 20 Volume
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
	
	private function ConnectionTest()
	{
	      $result = false;
	      If (Sys_Ping($this->ReadPropertyString("IPAddress"), 2000)) {
			IPS_LogMessage("IPS2AcerP5530","Angegebene IP ".$this->ReadPropertyString("IPAddress")." reagiert");
			$this->SetStatus(102);
		      	$result = true;
		}
		else {
			IPS_LogMessage("IPS2AcerP5530","IP ".$this->ReadPropertyString("IPAddress")." reagiert nicht!");
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
	
	private function GetParentID()
	{
		$ParentID = (IPS_GetInstance($this->InstanceID)['ConnectionID']);  
	return $ParentID;
	}
	
	private function GetParentStatus()
	{
		$Status = (IPS_GetInstance($this->GetParentID())['InstanceStatus']);  
	return $Status;
	}
	
	
}
?>
