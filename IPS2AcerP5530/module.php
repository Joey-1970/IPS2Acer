<?
class IPS2AcerP5530 extends IPSModule
{
	public function Destroy() 
	{
		//Never delete this line!
		parent::Destroy();
		$this->SetTimerInterval("KeepAlive", 0);
	}
	
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
           	$this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");
		$this->RegisterPropertyBoolean("Open", false);
	    	$this->RegisterPropertyString("IPAddress", "127.0.0.1");
		$this->RegisterTimer("KeepAlive", 0, 'I2VSX923_KeepAlive($_IPS["TARGET"]);');
		
		// Profile anlegen
		$this->RegisterProfileFloat("IPS2Pioneer.dB", "Melody", "", " dB", -80, 12, 0.5, 1);
		
		$this->RegisterProfileFloat("IPS2Pioneer.dBZone", "Melody", "", " dB", -80, 0, 2, 1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.BassTreble", "Music", "", " dB", -6, 6, 1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.Volume", "Shutter", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Volume", 0, "+", "Shutter", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Volume", 1, "-", "Shutter", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.Input", "Repeat", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Input", 0, "<", "Repeat", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Input", 1, ">", "Repeat", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.PanelKeyLock", "Lock", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.PanelKeyLock", 0, "LOCK OFF", "LockOpen", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.PanelKeyLock", 1, "PANEL KEY LOCK ON", "LockClosed", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.PanelKeyLock", 2, "PANEL KEY & VOLUME LOCK ON", "LockClosed", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.RemoteLock", "Lock", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.RemoteLock", 0, "LOCK OFF", "LockOpen", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.RemoteLock", 1, "LOCK ON", "LockClosed", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.Speaker", "Speaker", "", "", 0, 3, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Speaker", 0, "Speaker off", "Speaker", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Speaker", 1, "Speaker A on", "Speaker", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Speaker", 2, "Speaker B on", "Speaker", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Speaker", 3, "Speaker A+B on", "Speaker", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.Tone", "Music", "", "", 0, 1, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Tone", 0, "ByPass", "Music", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.Tone", 1, "On", "Music", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.HDMIOut", "TV", "", "", 0, 2, 0);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.HDMIOut", 0, "HDMI Out All", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.HDMIOut", 1, "HDMI Out 1", "TV", -1);
		IPS_SetVariableProfileAssociation("IPS2Pioneer.HDMIOut", 2, "HDMI Out 2", "TV", -1);
		
		$this->RegisterProfileInteger("IPS2Pioneer.ListeningModeSet", "Melody", "", "", 0, 128, 0);
		$this->SetListeningMode();
		
		$this->RegisterProfileInteger("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, "Speaker", "", "", 0, 14, 0);
		$this->SetSpeakerSystem();
		
		$this->RegisterProfileInteger("IPS2Pioneer.InputSelect_".$this->InstanceID, "Repeat", "", "", 0, 45, 0);
		
		
		$MetadataArray = array(1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "");
		$this->SetBuffer("Metadata", serialize($MetadataArray));
		
		$DeviceArray = array();
		$this->SetBuffer("Devices", serialize($DeviceArray));
		
		// Statusvariablen anlegen
		$this->RegisterVariableInteger("LastKeepAlive", "Letztes Keep Alive", "~UnixTimestamp", 10);
		$this->DisableAction("LastKeepAlive");
		
		$this->RegisterVariableBoolean("Power", "Power", "~Switch", 20);
		$this->EnableAction("Power");
		
		$this->RegisterVariableInteger("Input", "Input", "IPS2Pioneer.InputSelect_".$this->InstanceID, 30);
		$this->EnableAction("Input");
		
		$this->RegisterVariableInteger("InputChange", "Input", "IPS2Pioneer.Input", 40);
		$this->EnableAction("InputChange");
		
		$this->RegisterVariableFloat("Volume", "Volume", "IPS2Pioneer.dB", 50);
		$this->EnableAction("Volume");
		
		$this->RegisterVariableInteger("VolumeUpDown", "Volume", "IPS2Pioneer.Volume", 60);
		$this->EnableAction("VolumeUpDown");
		
		$this->RegisterVariableString("Display", "Display", "", 70);
		
		$this->RegisterVariableString("ListeningMode", "Listening Mode", "", 80);
		
		$this->RegisterVariableInteger("ListeningModeSet", "Listening Mode", "IPS2Pioneer.ListeningModeSet", 90);
		$this->EnableAction("ListeningModeSet");
		
		$this->RegisterVariableBoolean("Mute", "Mute", "~Switch", 100);
		$this->EnableAction("Mute");
		
		$this->RegisterVariableInteger("Speakers", "Speakers", "IPS2Pioneer.Speaker", 110);
		$this->EnableAction("Speakers");
		
		$this->RegisterVariableInteger("HDMIOut", "HDMI Out", "IPS2Pioneer.HDMIOut", 120);
		$this->EnableAction("HDMIOut");
		
		$this->RegisterVariableInteger("Tone", "Tone", "IPS2Pioneer.Tone", 200);
		$this->EnableAction("Tone");
		
		$this->RegisterVariableInteger("Bass", "Bass", "IPS2Pioneer.BassTreble", 210);
		$this->EnableAction("Bass");
		
		$this->RegisterVariableInteger("Treble", "Treble", "IPS2Pioneer.BassTreble", 220);
		$this->EnableAction("Treble");
		
		$this->RegisterVariableString("Metadata", "Metadata", "~TextBox", 300);
		
		$this->RegisterVariableInteger("PanelKeyLock", "Panel Key Lock", "IPS2Pioneer.PanelKeyLock", 310);
		$this->EnableAction("PanelKeyLock");
		
		$this->RegisterVariableInteger("RemoteLock", "Remote Lock", "IPS2Pioneer.RemoteLock", 320);
		$this->EnableAction("RemoteLock");
		
		$this->RegisterVariableBoolean("Zone_2", "Zone_2", "~Switch", 400);
		$this->EnableAction("Zone_2");
		
		$this->RegisterVariableInteger("Zone_2_Source", "Zone 2 Source", "IPS2Pioneer.InputSelect_".$this->InstanceID, 410);
		$this->EnableAction("Zone_2_Source");
		
		$this->RegisterVariableFloat("Zone_2_Volume", "Zone 2 Volume", "IPS2Pioneer.dBZone", 420);
		$this->EnableAction("Zone_2_Volume");
		
		
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
 		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
		
		
		
		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	} 
	
	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();
		$this->RegisterMediaObject("Cover", "Cover_".$this->InstanceID, 1, $this->InstanceID, 1000, true, "Cover.jpg");
		If (IPS_GetKernelRunlevel() == 10103) {
			$ParentID = $this->GetParentID();
			If ($ParentID > 0) {
				If (IPS_GetProperty($ParentID, 'Host') <> $this->ReadPropertyString('IPAddress')) {
		                	IPS_SetProperty($ParentID, 'Host', $this->ReadPropertyString('IPAddress'));
				}
				If (IPS_GetProperty($ParentID, 'Port') <> 8102) {
		                	IPS_SetProperty($ParentID, 'Port', 8102);
				}
				If (IPS_GetName($ParentID) == "Client Socket") {
		                	IPS_SetName($ParentID, "IPS2PioneerVSX923");
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
				$this->SetMetadata();
				$this->GetInputDevices();
				$this->GetInputName();
				$this->GetData();
				$this->SetTimerInterval("KeepAlive", 5 * 60 * 1000);
			}
			else {
				$this->SetStatus(104);
				$this->SetTimerInterval("KeepAlive", 0);
			}	   
		}
	}
	
	public function ReceiveData($JSONString) {
 	    	//IPS_SemaphoreLeave("Communication");
		// Empfangene Daten vom I/O
	    	SetValueInteger($this->GetIDForIdent("LastKeepAlive"), time() );
		$Data = json_decode($JSONString);
		$Message = utf8_decode($Data->Buffer);
		
		// Entfernen der Steuerzeichen
		$Message = trim($Message, "\x00..\x1F");
		
		$MessageParts = explode(chr(13), $Message);
		
		foreach ($MessageParts as $Message) {
			// Entfernen der Steuerzeichen
			$Message = trim($Message, "\x00..\x1F");
			$this->SendDebug("ReceiveData", $Message, 0);
		
			switch($Message) {
				case "E02":
					$this->SendDebug("ReceiveData", "E02: NOT AVAILABLE NOW", 0);
					break;
				case "E03":
					$this->SendDebug("ReceiveData", "E03: INVALID COMMAND", 0);
					break;
				case "E04":
					$this->SendDebug("ReceiveData", "E04: COMMAND ERROR", 0);
					break;
				case "E06":
					$this->SendDebug("ReceiveData", "E06: PARAMETER ERROR", 0);
					break;
				case "B00":
					$this->SendDebug("ReceiveData", "B00: BUSY", 0);
					break;
				case "PWR0":
					SetValueBoolean($this->GetIDForIdent("Power"), true);
					break;
				case "PWR1":
					SetValueBoolean($this->GetIDForIdent("Power"), false);
					break;
				case "MUT0":
					SetValueBoolean($this->GetIDForIdent("Mute"), true);
					break;
				case "MUT1":
					SetValueBoolean($this->GetIDForIdent("Mute"), false);
					break;
				case preg_match('/FN.*/', $Message) ? $Message : !$Message:
					SetValueInteger($this->GetIDForIdent("Input"), intval(substr($Message, -2)));
					break;
				case preg_match('/Z2F.*/', $Message) ? $Message : !$Message:
					SetValueInteger($this->GetIDForIdent("Zone_2_Source"), intval(substr($Message, -2)));
					break;
				case preg_match('/VOL.*/', $Message) ? $Message : !$Message:
					$Volume = intval(substr($Message, -3));
					$Volume = ($Volume - 161) / 2;
					SetValueFloat($this->GetIDForIdent("Volume"), $Volume);
					break;
				case preg_match('/ZV.*/', $Message) ? $Message : !$Message:
					$VolumeZone2 = intval(substr($Message, -2));
					$VolumeZone2 = ($VolumeZone2 - 81);
					SetValueFloat($this->GetIDForIdent("Zone_2_Volume"), $VolumeZone2);
					break;
				case preg_match('/FL.*/', $Message) ? $Message : !$Message:
					$Result = "";
					$Message = substr($Message, 2);
					$MessageArray = str_split($Message, 2);
					for ($i = 0; $i <= count($MessageArray) - 1; $i++) {
						If (hexdec($MessageArray[$i]) <> 0x02) {
							$Sign = chr(hexdec($MessageArray[$i]));
							$Result = $Result.$Sign;
						}
					}
					$Result = trim($Result, " \t\n\r\0\x0B");
					SetValueString($this->GetIDForIdent("Display"), $Result);
					break;	
				case preg_match('/LM.*/', $Message) ? $Message : !$Message:
					$Mode = substr($Message, -4);
					$ModeText = $this->GetListeningMode($Mode);
					SetValueString($this->GetIDForIdent("ListeningMode"), $ModeText);
					break;
				case preg_match('/SR.*/', $Message) ? $Message : !$Message:
					$ListenningMode = intval(substr($Message, -4));
					If (($ListenningMode == 6) OR ($ListenningMode == 9) OR ($ListenningMode == 153)) {
						$this->EnableAction("Tone");
						If (GetValueInteger($this->GetIDForIdent("Tone")) == 1) {
							$this->EnableAction("Bass");
							$this->EnableAction("Treble");
						}
						else {
							$this->DisableAction("Bass");
							$this->DisableAction("Treble");
						}
					}
					else {
						$this->DisableAction("Tone");
						$this->DisableAction("Bass");
						$this->DisableAction("Treble");
					}
					SetValueInteger($this->GetIDForIdent("ListeningModeSet"), $ListenningMode);
					break;	
				case preg_match('/SPK.*/', $Message) ? $Message : !$Message:
					$Speaker = intval(substr($Message, -1));
					SetValueInteger($this->GetIDForIdent("Speakers"), $Speaker);
					break;	
				case preg_match('/HO.*/', $Message) ? $Message : !$Message:
					$HDMIOut = intval(substr($Message, -1));
					SetValueInteger($this->GetIDForIdent("HDMIOut"), $HDMIOut);
					break;	
				case preg_match('/TO.*/', $Message) ? $Message : !$Message:
					$ToneByPass = intval(substr($Message, -1));
					If ($ToneByPass == 1) {
						$this->EnableAction("Bass");
						$this->EnableAction("Treble");
					}
					else {
						$this->DisableAction("Bass");
						$this->DisableAction("Treble");
					}
					SetValueInteger($this->GetIDForIdent("Tone"), $ToneByPass);
					break;	
				case preg_match('/BA.*/', $Message) ? $Message : !$Message:
					$Bass = intval(substr($Message, -2)) - 6;
					SetValueInteger($this->GetIDForIdent("Bass"), $Bass);
					break;	
				case preg_match('/TR.*/', $Message) ? $Message : !$Message:
					$Treble = intval(substr($Message, -2)) - 6;
					SetValueInteger($this->GetIDForIdent("Treble"), $Treble);
					break;	
				case preg_match('/GIC.*/', $Message) ? $Message : !$Message:
					preg_match('/"([^"]*)"/is', $Message, $Result);
					$this->GetCover($Result[1]);
					break;	
				case preg_match('/GEH.*/', $Message) ? $Message : !$Message:
					$MetadataArray = unserialize($this->GetBuffer("Metadata"));
					$Line = intval(substr($Message, 3, 2));
					$FocusInformation = intval(substr($Message, 5, 1));
					$DataType = intval(substr($Message, 6, 2));
					preg_match('/"([^"]*)"/is', $Message, $Result);
					$Text = $Result[1];
					$MetadataArray[$Line] = $Text;
					$this->SetBuffer("Metadata", serialize($MetadataArray));
					$this->SetMetadata();
					break;
				case "APR0":
					SetValueBoolean($this->GetIDForIdent("Zone_2"), true);
					break;
				case "APR1":
					SetValueBoolean($this->GetIDForIdent("Zone_2"), false);
					break;
				case preg_match('/PKL.*/', $Message) ? $Message : !$Message:
					$PanelKeyLock = intval(substr($Message, -1));
					SetValueInteger($this->GetIDForIdent("PanelKeyLock"), $PanelKeyLock);
					break;	
				case preg_match('/RML.*/', $Message) ? $Message : !$Message:
					$RemoteLock = intval(substr($Message, -1));
					SetValueInteger($this->GetIDForIdent("RemoteLock"), $RemoteLock);
					break;	
				case preg_match('/SSF.*/', $Message) ? $Message : !$Message:
					$SpeakerSystem = intval(substr($Message, -2));
					If ($SpeakerSystem < 3) {
						IPS_SetVariableProfileAssociation("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, 1, "Speaker A on", "Speaker", -1);
						IPS_SetVariableProfileAssociation("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, 2, "Speaker B on", "Speaker", -1);
					}
					else {
						IPS_SetVariableProfileAssociation("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, 1, "", "", -1);
						IPS_SetVariableProfileAssociation("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, 2, "", "", -1);
					}
					If ($SpeakerSystem == 4) {
						$this->EnableAction("Zone_2");
						$this->EnableAction("Zone_2_Source");
						$this->EnableAction("Zone_2_Volume");
					}
					else {
						$this->DisableAction("Zone_2");
						$this->DisableAction("Zone_2_Source");
						$this->DisableAction("Zone_2_Volume");
					}
					SetValueInteger($this->GetIDForIdent("SpeakerSystem"), $SpeakerSystem);
					break;	
				case preg_match('/SSC.*/', $Message) ? $Message : !$Message:
					$Devices = $this->GetBuffer("Devices");
					$DeviceArray = unserialize($Devices);
					$Device = intval(substr($Message, -6, 2));
					$Info = intval(substr($Message, -4, 2));
					
					If ($Info == 3) {
						$SkipUse = intval(substr($Message, -2));
						$DeviceArray[$Device]["Used"] = !boolval($SkipUse);
						$this->SendDebug("SSC", "Message: ".$Device.": ".$DeviceArray[$Device]["Used"]." ".$DeviceArray[$Device]["PioneerName"], 0);
						$this->SetBuffer("Devices", serialize($DeviceArray));
					}
					break;
				case preg_match('/RGB.*/', $Message) ? $Message : !$Message:
					$Devices = $this->GetBuffer("Devices");
					$DeviceArray = unserialize($Devices);
					$Message = substr($Message, 3);
					$Device = intval(substr($Message, 0, 2));
					$Rename = boolval(substr($Message, 2, 1));
					$Name = substr($Message, 3);
					
					$DeviceArray[$Device]["MyName"] = $Name;
					$this->SetBuffer("Devices", serialize($DeviceArray));
					If ($DeviceArray[$Device]["Used"] == true) {
						$this->SendDebug("ReceiveData", "Pioneer Nr: ".$Device." Mein Name: ".$Name , 0);
						IPS_SetVariableProfileAssociation("IPS2Pioneer.InputSelect_".$this->InstanceID, $Device, $Name, "Repeat", -1);
					}
					elseIf ($DeviceArray[$Device]["Used"] == false) {
						$Result = @IPS_SetVariableProfileAssociation("IPS2Pioneer.InputSelect_".$this->InstanceID, $Device, "", "", -1);
					}
					break;
			}
		}
	}
	
	public function RequestAction($Ident, $Value) 
	{
  		If (($this->ReadPropertyBoolean("Open") == true) AND ($this->GetParentStatus() == 102)) {
			switch($Ident) {
				case "Power":
					If (GetValueBoolean($this->GetIDForIdent("Power")) == true) {
						$this->SetData("PF");
					}
					else {
						$this->SetData("PO");
					}
					break;
				case "Mute":
					If (GetValueBoolean($this->GetIDForIdent("Mute")) == true) {
						$this->SetData("MF");
					}
					else {
						$this->SetData("MO");
					}
					break;
				case "Volume":
					$Volume = ($Value * 2) + 161; 
					$Volume = str_pad($Volume, 3, '0', STR_PAD_LEFT);
					$this->SetData($Volume."VL");
					break;
				case "Zone_2_Volume": 
					$VolumeZone2 = intval($Value + 81);
					$VolumeZone2 = str_pad($VolumeZone2, 2, '0', STR_PAD_LEFT);
					$this->SetData($VolumeZone2."ZV");
					break;
				case "VolumeUpDown":
					SetValueInteger($this->GetIDForIdent("VolumeUpDown"), $Value);
					If ($Value == 0) {
						$this->SetData("VU");
					}
					elseIf ($Value == 1) {
						$this->SetData("VD");
					}
					break;
				case "InputChange":
					SetValueInteger($this->GetIDForIdent("InputChange"), $Value);
					If ($Value == 0) {
						$this->SetData("FD");
					}
					elseIf ($Value == 1) {
						$this->SetData("FU");
					}
					break;
				case "Input":
					$Input = str_pad($Value, 2, '0', STR_PAD_LEFT);
					$this->SetData($Input."FN");
					break;
				case "Zone_2_Source":
					$Zone_2_Source = str_pad($Value, 2, '0', STR_PAD_LEFT);
					$this->SetData($Zone_2_Source."ZS");
					break;
				case "ListeningModeSet":
					$ListeningMode = str_pad($Value, 4, '0', STR_PAD_LEFT);
					$this->SetData($ListeningMode."SR");
					break;
				case "Speakers":
					$Speaker = intval($Value);
					$this->SetData($Speaker."SPK");
					break;
				case "PanelKeyLock":
					$PanelKeyLock = intval($Value);
					$this->SetData($PanelKeyLock."PKL");
					break;
				case "RemoteLock":
					$RemoteLock = intval($Value);
					$this->SetData($RemoteLock."RML");
					break;
				case "HDMIOut":
					$HDMIOut = intval($Value);
					$this->SetData($HDMIOut."HO");
					break;
				case "Tone":
					$Tone = intval($Value);
					$this->SetData($Tone."TO");
					break;
				case "Bass":
					$Bass = intval($Value) + 6;
					$Bass = str_pad($Bass, 2, '0', STR_PAD_LEFT);
					$this->SetData($Bass."BA");
					break;
				case "Treble":
					$Treble = intval($Value) + 6;
					$Treble = str_pad($Treble, 2, '0', STR_PAD_LEFT);
					$this->SetData($Treble."TR");
					break;
				case "Zone_2":
					If ($Value == true) {
						$this->SetData("APO");
					}
					else {
						$this->SetData("APF");
					}
					break;
				case "SpeakerSystem":
					$SpeakerSystem = str_pad($Value, 2, '0', STR_PAD_LEFT);
					$this->SetData($SpeakerSystem."SSF");
					break;
				default:
				    throw new Exception("Invalid Ident");
			}
	    	}
		
	}
	
	private function GetData()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetData", "Ausfuehrung", 0);
			$MessageArray = array("?P", "?F", "?V", "?FL", "?M", "?L", "?S", "?SPK", "?TO", "?BA", "?TR", "?GIC", "?AP", "?HO", "?ZS", "?ZV", "?SSF", "?PKL", "?RML");
			foreach ($MessageArray as $Value) {
				$Message = $Value.chr(13);
				$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
				IPS_Sleep(100);
			}
			$Content = file_get_contents(__DIR__ . '/../imgs/Pioneer.jpg');
			IPS_SetMediaContent($this->GetIDForIdent("Cover_".$this->InstanceID), base64_encode($Content));  //Bild Base64 codieren und ablegen                                 
			IPS_SendMediaEvent($this->GetIDForIdent("Cover_".$this->InstanceID)); //aktualisieren
		}
	}
	
	private function SetData(String $Message)
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("SetData", "Message: ".$Message, 0);
			$Message = $Message.chr(13);
			$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Message))));
		}
	}
	
	public function KeepAlive()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("KeepAlive", "Ausfuehrung", 0);
			$Result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode("?P".chr(13)))));
		}
	}
	
	private function GetCover(String $URL = null)
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			if ($URL != null) { 
              			$Content = file_get_contents($URL); 
          		} else { 
              			$Content = file_get_contents(__DIR__ . '/../imgs/Pioneer.jpg'); 
          		} 
			IPS_SetMediaContent($this->GetIDForIdent("Cover_".$this->InstanceID), base64_encode($Content));  //Bild Base64 codieren und ablegen
			IPS_SendMediaEvent($this->GetIDForIdent("Cover_".$this->InstanceID)); //aktualisieren
		}
	} 
	
	private function SetMetadata()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MetadataArray = unserialize($this->GetBuffer("Metadata"));
			$Value = "";
			for ($i = 1; $i <= 8; $i++) {
				$Value = $Value.$MetadataArray[$i].chr(13);
			}
			SetValueString($this->GetIDForIdent("Metadata"), $Value);
		}
	} 
	
	public function PowerOn()
	{
		If (($this->ReadPropertyBoolean("Open") == true) AND ($this->GetParentStatus() == 102)) {
			$this->SetData("PO");
		}	
	}
	
	public function PowerOff()
	{
		If (($this->ReadPropertyBoolean("Open") == true) AND ($this->GetParentStatus() == 102)) {
			$this->SetData("PF");
		}	
	}
	
	private function GetListeningMode(String $ListeningMode)
	{
		$Mode = array("0101" => "[)(]PLIIx MOVIE", "0102" => "[)(]PLII MOVIE", "0103" => "[)(]PLIIx MUSIC", 
				  "0104" => "[)(]PLII MUSIC", "0105" => "[)(]PLIIx GAME", "0106" => "[)(]PLII GAME", 
				  "0107" => "[)(]PROLOGIC", "0108" => "Neo", "0109" => "Neo", "010a" => "XM HD Surround", 
				  "010b" => "NEURAL SURR", "010c" => "2ch Straight Decode", "010d" => "[)(]PLIIz HEIGHT", 
				  "010e" => "WIDE SURR MOVIE", "010f" => "WIDE SURR MUSIC", "0110" => "STEREO", "0111" => "Neo", 
				  "0112" => "Neo", "0113" => "Neo", "0114" => "NEURAL SURROUND+Neo", "0115" => "NEURAL SURROUND+Neo", 
				  "0116" => "NEURAL SURROUND+Neo", "1101" => "[)(]PLIIx MOVIE", "1102" => "[)(]PLIIx MUSIC", 
				  "1103" => "[)(]DIGITAL EX", "1104" => "DTS +Neo", "1105" => "ES MATRIX", "1106" => "ES DISCRETE", 
				  "1107" => "DTS-ES 8ch", "1108" => "multi ch Straight Decode", "1109" => "[)(]PLIIz HEIGHT", 
				  "110a" => "WIDE SURR MOVIE", "110b" => "WIDE SURR MUSIC", "110c" => "ES Neo", "0201" => "ACTION", 
				  "0202" => "DRAMA", "0203" => "SCI-FI", "0204" => "MONOFILM", "0205" => "ENT.SHOW", 
				  "0206" => "EXPANDED", "0207" => "TV SURROUND", "0208" => "ADVANCEDGAME", "0209" => "SPORTS", 
				  "020a" => "CLASSICAL", "020b" => "ROCK/POP", "020c" => "UNPLUGGED", "020d" => "EXT.STEREO", 
				  "020e" => "PHONES SURR.", "020f" => "FRONT STAGE SURROUND ADVANCE FOCUS", 
				  "0210" => "FRONT STAGE SURROUND ADVANCE WIDE", "0211" => "SOUND RETRIEVER AIR", 
				  "0301" => "[)(]PLIIx MOVIE +THX", "0302" => "[)(]PLII MOVIE +THX", "0303" => "[)(]PL +THX CINEMA", 
				  "0304" => "Neo", "0305" => "THX CINEMA", "0306" => "[)(]PLIIx MUSIC +THX", 
				  "0307" => "[)(]PLII MUSIC +THX", "0308" => "[)(]PL +THX MUSIC", "0309" => "Neo", 
				  "030a" => "THX MUSIC", "030b" => "[)(]PLIIx GAME +THX", "030c" => "[)(]PLII GAME +THX", 
				  "030d" => "[)(]PL +THX GAMES", "030e" => "THX ULTRA2 GAMES", "030f" => "THX SELECT2 GAMES", 
				  "0310" => "THX GAMES", "0311" => "[)(]PLIIz +THX CINEMA", "0312" => "[)(]PLIIz +THX MUSIC", 
				  "0313" => "[)(]PLIIz +THX GAMES", "0314" => "Neo", "0315" => "Neo", "0316" => "Neo", 
				  "1301" => "THX Surr EX", "1302" => "Neo", "1303" => "ES MTRX +THX CINEMA", 
				  "1304" => "ES DISC +THX CINEMA", "1305" => "ES 8ch +THX CINEMA", "1306" => "[)(]PLIIx MOVIE +THX", 
				  "1307" => "THX ULTRA2 CINEMA", "1308" => "THX SELECT2 CINEMA", "1309" => "THX CINEMA", 
				  "130a" => "Neo", "130b" => "ES MTRX +THX MUSIC", "130c" => "ES DISC +THX MUSIC", 
				  "130d" => "ES 8ch +THX MUSIC", "130e" => "[)(]PLIIx MUSIC +THX", "130f" => "THX ULTRA2 MUSIC", 
				  "1310" => "THX SELECT2 MUSIC", "1311" => "THX MUSIC", "1312" => "Neo", 
				  "1313" => "ES MTRX +THX GAMES", "1314" => "ES DISC +THX GAMES", "1315" => "ES 8ch +THX GAMES", 
				  "1316" => "[)(]EX +THX GAMES", "1317" => "THX ULTRA2 GAMES", "1318" => "THX SELECT2 GAMES", 
				  "1319" => "THX GAMES", "131a" => "[)(]PLIIz +THX CINEMA", "131b" => "[)(]PLIIz +THX MUSIC", 
				  "131c" => "[)(]PLIIz +THX GAMES", "131d" => "Neo", "131e" => "Neo", "131f" => "Neo", 
				  "0401" => "STEREO", "0402" => "[)(]PLII MOVIE", "0403" => "[)(]PLIIx MOVIE", "0404" => "Neo", 
				  "0405" => "AUTO SURROUND Straight Decode", "0406" => "[)(]DIGITAL EX", "0407" => "[)(]PLIIx MOVIE", 
				  "0408" => "DTS +Neo", "0409" => "ES MATRIX", "040a" => "ES DISCRETE", "040b" => "DTS-ES 8ch",
				  "040c" => "XM HD Surround", "040d" => "NEURAL SURR", "040e" => "RETRIEVER AIR", "040f" => "Neo", 
				  "0410" => "ES Neo", "0501" => "STEREO", "0502" => "[)(]PLII MOVIE", "0503" => "[)(]PLIIx MOVIE", 
				  "0504" => "Neo", "0505" => "ALC Straight Decode", "0506" => "[)(]DIGITAL EX", 
				  "0507" => "[)(]PLIIx MOVIE", "0508" => "DTS +Neo", "0509" => "ES MATRIX", "050a" => "ES DISCRETE", 
				  "050b" => "DTS-ES 8ch", "050c" => "XM HD Surround", "050d" => "NEURAL SURR", 
				  "050e" => "RETRIEVER AIR", "050f" => "Neo", "0510" => "ES Neo", "0601" => "STEREO", 
				  "0602" => "[)(]PLII MOVIE", "0603" => "[)(]PLIIx MOVIE", "0604" => "Neo", 
				  "0605" => "STREAM DIRECT NORMAL Straight Decode", "0606" => "[)(]DIGITAL EX", 
				  "0607" => "[)(]PLIIx MOVIE", "0608" => "(nothing)", "0609" => "ES MATRIX", "060a" => "ES DISCRETE", 
				  "060b" => "DTS-ES 8ch", "060c" => "Neo", "060d" => "ES Neo", "0701" => "STREAM DIRECT PURE 2ch", 
				  "0702" => "[)(]PLII MOVIE", "0703" => "[)(]PLIIx MOVIE", "0704" => "Neo", 
				  "0705" => "STREAM DIRECT PURE Straight Decode", "0706" => "[)(]DIGITAL EX", 
				  "0707" => "[)(]PLIIx MOVIE", "0708" => "(nothing)", "0709" => "ES MATRIX", "070a" => "ES DISCRETE", 
				  "070b" => "DTS-ES 8ch", "070c" => "Neo", "070d" => "ES Neo", "0881" => "OPTIMUM", 
				  "0e01" => "HDMI THROUGH", "0f01" => "MULTI CH IN");
		If (array_key_exists($ListeningMode, $Mode)) {
			$ListingModeText = $Mode[$ListeningMode];
		}
		else {
			$ListingModeText = "Unbekannter Listening Mode!";
		}
		
	return $ListingModeText;
	}
	
	private function SetListeningMode()
	{
		$Mode = array(10 => "STANDARD", 9 => "STEREO (direct set)",
			      13 => "PRO LOGIC2 MOVIE", 18 => "PRO LOGIC2x MOVIE", 14 => "PRO LOGIC2 MUSIC", 19 => "PRO LOGIC2x MUSIC", 
			      15 => "PRO LOGIC2 GAME", 20 => "PRO LOGIC2x GAME", 31 => "PRO LOGIC2z HEIGHT", 32 => "WIDE SURROUND MOVIE", 
			      33 => "WIDE SURROUND MUSIC", 12 => "PRO LOGIC", 16 => "Neo", 17 => "Neo", 21 => "(Multi ch source)", 
			      22 => "(Multi ch source)+DOLBY EX", 23 => "(Multi ch source)+PRO LOGIC2x MOVIE", 
			      24 => "(Multi ch source)+PRO LOGIC2x MUSIC", 34 => "(Multi-ch Source)+PRO LOGIC2z HEIGHT", 
			      35 => "(Multi-ch Source)+WIDE SURROUND MOVIE", 36 => "(Multi-ch Source)+WIDE SURROUND MUSIC", 
			      25 => "(Multi ch source)DTS-ES Neo", 26 => "(Multi ch source)DTS-ES matrix", 
			      27 => "(Multi ch source)DTS-ES discrete", 30 => "(Multi ch source)DTS-ES 8ch discrete", 
			      100 => "ADVANCED SURROUND (cyclic)", 101 => "ACTION", 103 => "DRAMA", 102 => "SCI-FI", 105 => "MONO FILM", 
			      104 => "ENTERTAINMENT SHOW", 106 => "EXPANDED THEATER", 116 => "TV SURROUND", 118 => "ADVANCED GAME", 
			      117 => "SPORTS", 107 => "CLASSICAL", 110 => "ROCK/POP", 109 => "UNPLUGGED", 112 => "EXTENDED STEREO", 
			      3 => "Front Stage Surround Advance Focus", 4 => "Front Stage Surround Advance Wide", 153 => "RETRIEVER AIR", 
			      113 => "PHONES SURROUND", 93 => "PL2z HEIGHT + THX MUSIC", 5 => "AUTO SURR/STREAM DIRECT (cyclic)", 6 => "AUTO SURROUND", 
			      151 => "Auto Level Control (A.L.C.)", 7 => "DIRECT", 8 => "PURE DIRECT");
		foreach ($Mode as $Key => $Value) {
			IPS_SetVariableProfileAssociation("IPS2Pioneer.ListeningModeSet", $Key, $Value, "Melody", -1);
		}
	return;
	}
	
	private function SetSpeakerSystem()
	{
		$Mode = array(0 => "Normal(SB/FH)", 1 => "Normal(SB/FW)", 2 => "Speaker B", 3 => "Front Bi-Amp", 4 => "ZONE 2");
		foreach ($Mode as $Key => $Value) {
			IPS_SetVariableProfileAssociation("IPS2Pioneer.SpeakerSystem_".$this->InstanceID, $Key, $Value, "Speaker", -1);
		}
	return;
	}
	
	private function SetZoneSources()
	{
		$Mode = array(4 => "DVD", 6 => "SAT/CBL", 15 => "DVR/BDR", 5 => "TV", 1 => "CD", 2 => "TUNER", 33 => "ADAPTER PORT");
		foreach ($Mode as $Key => $Value) {
			IPS_SetVariableProfileAssociation("IPS2Pioneer.ZoneSources", $Key, $Value, "Speaker", -1);
		}
	return;
	}
	
	private function GetInputDevices()
	{
		$PioneerDevices = array(25 => "BD", 4 => "DVD", 6 => "SAT/CBL", 15 => "DVR/BDR", 19 => "HDMI 1", 20 => "HDMI 2", 
			      21 => "HDMI 3", 22 => "HDMI 4", 23 => "HDMI 5", 24 => "HDMI 6", 34 => "HDMI 7", 38 => "INTERNET RADIO", 
			      44 => "MEDIA SERVER", 45 => "FAVORITES", 17 => "iPod/USB", 5 => "TV", 1 => "CD", 
			      2 => "TUNER", 33 => "ADAPTER PORT");
		$Devices = $this->GetBuffer("Devices");
		$DeviceArray = unserialize($Devices);
		
		foreach ($PioneerDevices as $Key => $Value) {
			$DeviceArray[$Key]["PioneerName"] = $Value;
			$DeviceArray[$Key]["Used"] = false;
			$DeviceArray[$Key]["MyName"] = $Value;
			$DeviceNumber = str_pad($Key, 2, '0', STR_PAD_LEFT);
			$this->SetData("?SSC".$DeviceNumber."03");
		}
		$this->SetBuffer("Devices", serialize($DeviceArray));
	return;
	}
	
	private function GetInputName()
	{
		$Devices = $this->GetBuffer("Devices");
		$DeviceArray = unserialize($Devices);
		foreach ($DeviceArray as $Key => $Value) {
			$DeviceNumber = str_pad($Key, 2, '0', STR_PAD_LEFT);
			$this->SetData("?RGB".$DeviceNumber);
			
			If ($DeviceArray[$Key]["Used"] == true)
			{
				
			}
		}
	}
	
	private function ConnectionTest()
	{
	      $result = false;
	      If (Sys_Ping($this->ReadPropertyString("IPAddress"), 2000)) {
			//IPS_LogMessage("IPS2PioneerBDP450","Angegebene IP ".$this->ReadPropertyString("IPAddress")." reagiert");
			$status = @fsockopen($this->ReadPropertyString("IPAddress"), 8102, $errno, $errstr, 10);
				if (!$status) {
					IPS_LogMessage("IPS2PioneerBDP450","Port ist geschlossen!");				
	   			}
	   			else {
	   				fclose($status);
					//IPS_LogMessage("IPS2PioneerBDP450","Port ist geöffnet");
					$result = true;
					$this->SetStatus(102);
	   			}
		}
		else {
			IPS_LogMessage("IPS2PioneerBDP450","IP ".$this->ReadPropertyString("IPAddress")." reagiert nicht!");
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
	
	private function RegisterMediaObject($Name, $Ident, $Typ, $Parent, $Position, $Cached, $Filename)
	{
		$MediaID = @$this->GetIDForIdent($Ident);
		if($MediaID === false) {
		    	$MediaID = 0;
		}
		
		if ($MediaID == 0) {
			 // Image im MedienPool anlegen
			$MediaID = IPS_CreateMedia($Typ); 
			// Medienobjekt einsortieren unter Kategorie $catid
			IPS_SetParent($MediaID, $Parent);
			IPS_SetIdent($MediaID, $Ident);
			IPS_SetName($MediaID, $Name);
			IPS_SetPosition($MediaID, $Position);
                    	IPS_SetMediaCached($MediaID, $Cached);
			$ImageFile = IPS_GetKernelDir()."media".DIRECTORY_SEPARATOR.$Filename;  // Image-Datei
			IPS_SetMediaFile($MediaID, $ImageFile, false);    // Image im MedienPool mit Image-Datei verbinden
		}  
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
