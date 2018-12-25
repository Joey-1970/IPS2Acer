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
           	$this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");
		$this->RegisterPropertyBoolean("Open", false);
	    	$this->RegisterPropertyString("IPAddress", "127.0.0.1");
		$this->RegisterPropertyInteger("Port", 0);
		$this->RegisterTimer("State", 0, 'IPS2AcerP5530_State($_IPS["TARGET"]);');
		
		// Profile anlegen
		$this->RegisterProfileInteger("IPS2AcerP5530.Source", "TV", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 0, "HDMI 1", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 1, "HDMI 2", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 2, "Media", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 3, "USB Display", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Source", 4, "Analog RGB for D-Sub", "TV", -1);
		
		$this->RegisterProfileInteger("IPS2AcerP5530.Control", "Move", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Control", 0, "Left", "Move", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Control", 1, "Up", "Move", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Control", 2, "Enter", "Move", 0x0000FF);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Control", 3, "Down", "Move", -1);
		IPS_SetVariableProfileAssociation("IPS2AcerP5530.Control", 4, "Right", "Move", -1);
		
		// Statusvariablen anlegen
		$this->RegisterVariableInteger("LastKeepAlive", "Letztes Keep Alive", "~UnixTimestamp", 10);
		$this->DisableAction("LastKeepAlive");
		
		$this->RegisterVariableString("Name", "Name", "", 20);
		$this->RegisterVariableString("Model", "Model", "", 30);
		$this->RegisterVariableString("Res", "Resolution", "", 40);
		
		$this->RegisterVariableBoolean("Power", "Power", "~Switch", 50);
		$this->EnableAction("Power");
		
		$this->RegisterVariableInteger("Control", "Control", "IPS2AcerP5530.Control", 60);
		$this->EnableAction("Control");
		
		$this->RegisterVariableBoolean("ECO", "ECO", "~Switch", 70);
		$this->EnableAction("ECO");
		
		$this->RegisterVariableInteger("Source", "Source", "IPS2AcerP5530.Source", 80);
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
		$arrayElements[] = array("type" => "NumberSpinner", "name" => "Port",  "caption" => "Port"); 
 		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
		
		
		
		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	} 
	
	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();
		
		If (IPS_GetKernelRunlevel() == 10103) {
			$ParentID = $this->GetParentID();
			If ($ParentID > 0) {
				If (IPS_GetProperty($ParentID, 'Host') <> $this->ReadPropertyString('IPAddress')) {
		                	IPS_SetProperty($ParentID, 'Host', $this->ReadPropertyString('IPAddress'));
				}
				If (IPS_GetProperty($ParentID, 'Port') <> $this->ReadPropertyInteger('Port')) {
		                	IPS_SetProperty($ParentID, 'Port', $this->ReadPropertyInteger('Port'));
				}
				If (IPS_GetProperty($ParentID, 'Open') <> $this->ReadPropertyBoolean("Open")) {
		                	IPS_SetProperty($ParentID, 'Open', $this->ReadPropertyBoolean("Open"));
				}
				If (IPS_GetName($ParentID) == "Client Socket") {
		                	IPS_SetName($ParentID, "IPS2Acer5530");
				}
				if(IPS_HasChanges($ParentID))
				{
				    	$Result = @IPS_ApplyChanges($ParentID);
					If ($Result) {
						$this->SendDebug("ApplyChanges", "Einrichtung des Client Socket erfolgreich", 0);
					}
					else {
						$this->SendDebug("ApplyChanges", "Einrichtung des Client Socket nicht erfolgreich!", 0);
					}
				}
			}
			
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
	
	public function ReceiveData($JSONString) 
	{
 	    	SetValueInteger($this->GetIDForIdent("LastKeepAlive"), time() );
		// Empfangene Daten vom I/O
		$Data = json_decode($JSONString);
		$Message = utf8_decode($Data->Buffer);
		
		// Entfernen der Steuerzeichen
		$Message = trim($Message, "\x00..\x1F");
		
		$LastMessage = $this->GetBuffer("LastMessage");
		$this->SendDebug("ReceiveData", "Letze Message: ".$LastMessage." Antwort: ".$Message, 0);
		
		$MessageParts = explode(chr(13), $Message);
		
		foreach ($MessageParts as $Message) {
			// Entfernen der Steuerzeichen
			$Message = trim($Message, "\x00..\x1F");
			$this->SendDebug("ReceiveData", $Message, 0);
		
			switch($Message) {
				case "LAMP 0":
					If (GetValueBoolean($this->GetIDForIdent("Power")) == true) {
						SetValueBoolean($this->GetIDForIdent("Power"), false);
					}
					break;
				case "LAMP 1":
					If (GetValueBoolean($this->GetIDForIdent("Power")) == false) {
						SetValueBoolean($this->GetIDForIdent("Power"), true);
						$this->GetData();
					}
					break;
				case "ECO 0":
					SetValueBoolean($this->GetIDForIdent("ECO"), false);
					break;
				case "ECO 1":
					SetValueBoolean($this->GetIDForIdent("ECO"), true);
					break;
				case preg_match('/Model.*/', $Message) ? $Message : !$Message:
					SetValueString($this->GetIDForIdent("Model"), substr($Message, 6, -4));
					break;
				case preg_match('/Name.*/', $Message) ? $Message : !$Message:
					SetValueString($this->GetIDForIdent("Name"), substr($Message, 5));
					break;
				case preg_match('/Res.*/', $Message) ? $Message : !$Message:
					SetValueString($this->GetIDForIdent("Res"), substr($Message, 4));
					break;
				case "*000":
					$this->SendDebug("ReceiveData", "Abfrage erfolgreich!", 0);
					break;
				case "*001":
					$this->SendDebug("ReceiveData", "Abfrage aktuell nicht moeglich!", 0);
					break;
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
