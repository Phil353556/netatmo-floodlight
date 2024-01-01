#!/usr/bin/php
<?php
/*------------------------------------------------------------------------------*/
/* 2024/01/01 (c) Phil353556 github.com                                         */
/*  email: 51y9oj579@relay.firefox.com                                          */
/*                                                                              */
/* This piece of software is as IS with no Warranty. Use it of your own risk!   */
/* See LICENCE.md file in repository                                            */
/*------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------*/
/* The goal is to manage floodlight for external camera from Netatmo brand	*/
/* Run the script without any parameter to display the help			*/
/*------------------------------------------------------------------------------*/
/* list of function used							*/
/*										*/
/* function get_token($grant_type,$client_id,$client_secret,$scope,$Content_Type)*/
/* function get_homedata($access_token,$home_id)				*/
/* function get_homedata_forhome($access_token)					*/
/* function get_homestatus_NOC($access_token,$home_id)				*/
/* function usage()								*/
/* function set_light_mode($access_token,$home_id,$id,$state)			*/
/* function get_light_mode($access_token,$home_id,$id)				*/
/*------------------------------------------------------------------------------*/
/* Return codes 								*/
/* 0 : ok									*/
/* 1 : no option or option is usage or invalid option 				*/
/* 2 : exit following homelist option						*/
/* 3 : no external camera for this home in function get_homedata_forhome	*/
/* 4 : no external camera detected in function get_homestatus_NOC		*/
/* 5 : Invalid device id in function get_light_mode				*/
/* 6 : error doing action in function in function set_light			*/
/* 7 : invalid client or invalid grant in function get_token: check contents of variable*/
/*------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------*/
/* ONLY modify these FOUR variables with your data 				*/
/*------------------------------------------------------------------------------*/
$client_id="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$client_secret="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

$access_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$refresh_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
/*------------------------------------------------------------------------------*/
// Scopes
// Access tokens are associated to a scope, when requesting one, you should precise which scope you require. There are nine available scopes:
//     read_station: to retrieve weather station data (Getstationsdata, Getmeasure)
//     read_thermostat: to retrieve thermostat data ( Homestatus, Getroommeasure...)
//     write_thermostat: to set up the thermostat (Synchomeschedule, Setroomthermpoint...)
//     read_camera: to retrieve Smart Indoor Cameradata (Gethomedata, Getcamerapicture...)
//     write_camera: to inform the Smart Indoor Camera that a specific person or everybody has left the Home (Setpersonsaway, Setpersonshome)
//     access_camera: to access the camera, the videos and the live stream *
//     read_presence: to retrieve Smart Outdoor Camera data (Gethomedata, Getcamerapicture...)
//     access_presence: to access the camera, the videos and the live stream *
//     read_smokedetector : to retrieve the Smart Smoke Alarm informations and events (Gethomedata, Geteventsuntil...)
//     read_homecoach: to read data coming from Smart Indoor Air Quality Monitor (gethomecoachsdata)
// If no scope is provided during the token request, the default is "read_station"
/*------------------------------------------------------------------------------*/
/* This is the minimum scope mandatory for this script -------------------------*/
/* and yes access_presence is mandatory to get the current mode ----------------*/
/* otherwise error at the end of function get_light_mode 			*/
/* PHP Warning:  Undefined array key "floodlight" in /home/phermes/netatmo_light.php  */
/*------------------------------------------------------------------------------*/
$grant_type = "refresh_token";
$scope = "read_presence write_presence access_presence";
$Content_Type="application/x-www-form-urlencoded;charset=UTF-8";

/*------------------------------------------------------------------------------*/
/* Get authorization from netatmo using client_id, client_secret and *token  ---*/
/*------------------------------------------------------------------------------*/
function get_token($grant_type,$client_id,$client_secret,$scope,$Content_Type)
{
global $access_token;
global $refresh_token;

$handle = curl_init();
$datas = array("grant_type"=>$grant_type,"client_id"=>$client_id,"scope"=>$scope);

global $DEBUG;
if (( $DEBUG == true))
{
	print "grant type : $grant_type \n";
	print "client_id : $client_id \n";
	print "client_secret : $client_secret \n";
	print "refresh_token : $refresh_token \n";
	print "scope : $scope \n";
}
$datas = array("grant_type"=>$grant_type,"client_id"=>$client_id,"client_secret"=>$client_secret,"refresh_token"=>$refresh_token,"scope"=>$scope);

curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/oauth2/token",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $datas,
        CURLOPT_RETURNTRANSFER => true
        )
);

$result=curl_exec($handle);
curl_close($handle);
$array = json_decode($result, true);
if ( $DEBUG == true )
{
 	var_dump($array);
}

if ( isset($array['error']) )
	{
		print " ERROR ".$array['error']."\n";
		if ( isset($array['error_description']) )
		{
		print " ERROR ".$array['error_description']."\n";
		}
		exit(7);
	}
$handle = curl_init();

return [$array['access_token'],$array['refresh_token'],$array['expires_in']]; 
}

/* --------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------- */
function get_homedata($access_token,$home_id)
{
global $DEBUG;
if ( $DEBUG == true )
{	
	echo " ------------------------------------------------------------- \n";
	echo " GET homedata \n";
	echo " ------------------------------------------------------------- \n";
}	

$datas = array('NOC');
$datas2 = array(
        'home_id' => $home_id,
        'device_types' => $datas
);
$datas3 = json_encode($datas2);


$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homesdata",
        CURLOPT_POSTFIELDS => $datas3,
        CURLOPT_RETURNTRANSFER => true
        )
);

curl_setopt($handle, CURLOPT_VERBOSE, false);
curl_setopt($handle, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: Bearer ' . $access_token
	)
);

$result=curl_exec($handle);
curl_close($handle);
$array = json_decode($result, true);
if ( $DEBUG == true ) 
{
	var_dump($array);
}

if (!isset ($array['body']['homes'][0]['modules']))
{
	print "NO CAMERA\n";
	exit(3);
}
return [$array];

}

/* --------------------------------------------------------------------------- */
/* Get list of available home ------------------------------------------------ */
/* --------------------------------------------------------------------------- */
function get_homedata_forhome($access_token)
{
global $DEBUG;
if ( $DEBUG == true )
{	
	echo " ------------------------------------------------------------- \n";
	echo " GET homedata \n";
	echo " ------------------------------------------------------------- \n";
}	


$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homesdata",
        CURLOPT_RETURNTRANSFER => true
        )
);

curl_setopt($handle, CURLOPT_VERBOSE, false);
curl_setopt($handle, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: Bearer ' . $access_token
	)
);

$result=curl_exec($handle);
curl_close($handle);
global $DEBUG;
$array = json_decode($result, true);
if ( $DEBUG == true ) 
{
	var_dump($array);
}

if (isset ($array['body']['homes']) )
{
	foreach ($array['body']['homes'] as $value)
	{
	print " ".$value['id']." ".$value['name']."\n";
	}
}
return [$array];

}

/* --------------------------------------------------------------------------- */
/* Get -- P R O D U C T I O N ------------------------------------------------ */
/* --------------------------------------------------------------------------- */
function get_homestatus_NOC($access_token,$home_id)
{
global $DEBUG;
if ( $DEBUG == true )
{
	echo " ------------------------------------------------------------- \n";
	echo " GET homestatus \n";
	echo " ------------------------------------------------------------- \n";
}

$datas = array('NOC');
$datas2 = array(
        'home_id' => $home_id,
	'device_types' => $datas
);
$datas3 = json_encode($datas2);

$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homestatus",
        CURLOPT_POSTFIELDS => $datas3,
        CURLOPT_RETURNTRANSFER => true
        )
);

curl_setopt($handle, CURLOPT_VERBOSE, false);
curl_setopt($handle, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: Bearer ' . $access_token
	)
);

$result=curl_exec($handle);
curl_close($handle);
$array = json_decode($result, true);
if ( isset($array['error']) )
	{
		print " ERROR ".$array['error']['message']."\n";
		exit(4);
	}
if (!isset ($array['body']))
{
	print "NO CAMERA\n";
	print " ERROR ".$array['error']['message']."\n";
	exit(4);
}
if ( $DEBUG == true )
{
	var_dump($array);
}
return [$array];
}

/*------------------------------------------------------------------------------*/
/* Display the help for this script                                             */
/*------------------------------------------------------------------------------*/
function usage()
{
echo " ---------------------------------------------------------------------- \n";
echo " Usage: [usage|home id|homelist] <light> [auto|on|off|current|list] [list|device id] \n";
echo "                                                                        \n";
echo " usage                                                                  \n";
echo "          this help                                                     \n";
echo "                                                                        \n";
echo " home id                                                                \n";
echo "          home id for an home. Use homelist first to get home id        \n";
echo "          000000000000000000000000 (example format)                     \n"; 
echo "                                                                        \n";
echo " light                                                                  \n";
echo "          action will be applied on this device                         \n";
echo "                                                                        \n";
echo " <light action>                                                         \n";
echo "          auto or on or off or current or list                          \n";
echo "                                                                        \n";
echo " device id                                                              \n";
echo "          use list without <device id> first, to obtain device id       \n";
echo "          00:00:00:00:00:00 (example format)                            \n";
echo " ---------------------------------------------------------------------- \n";
}

/*------------------------------------------------------------------------------*/
/* Action done on external foodlight -------------------------------------------*/
/*------------------------------------------------------------------------------*/
function set_light_mode($access_token,$home_id,$id,$state)
{

$datas = '{"home":{"id":"'.$home_id.'","modules":[{"id":"'.$id.'","floodlight":"'.$state.'"}]}}';

$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/setstate",
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $datas,
        CURLOPT_RETURNTRANSFER => true
        )
);

curl_setopt($handle, CURLOPT_VERBOSE, false);
curl_setopt($handle, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: Bearer ' . $access_token
   ));

$result=curl_exec($handle);
curl_close($handle);
$array = json_decode($result, true);

if ( isset($array['error']) )
	{
		print " ERROR ".$array['error']['message']."\n";
		exit(6);
	}
}

/*------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------*/
function get_light_mode($access_token,$home_id,$id)
{
global $DEBUG;
[ $result ] = get_homestatus_NOC($access_token,$home_id);  
if ( $DEBUG == true )
{
	print "  get light mode \n";
}
$found = 0;
foreach ($result['body']['home']['modules'] as $value)
{
	if ( $DEBUG == true )
	{
		print " Light : ".$value['floodlight']."\n";
		print " id : ".$value['id']."\n";
		print " id cli : ".$id."\n";
	}
	$pos = strpos($value['type'],'NOC');
	if  ( $pos == 0 )
	{
		if ( $DEBUG == true) 
		{
		       print " Light : ".$value['floodlight']."\n";
		}
		if ( $id == $value['id'] )
		{
			 $found = 1;
		}

		if ( $DEBUG == true )
		{
			print " Camera : ".$value['type']."\n";
			print " firmware : ".$value['firmware_revision']."\n";
			print " id : ".$value['id']."\n";
			print " Wifi state : ".$value['wifi_state']."\n";
			print " Wifi quality : ".$value['wifi_strength']."\n";
		}
	}
   if ( $found == 1 ) 
   {
	   break;
   }
}
if ( $found == 0 ) 
{
	print " ERROR Invalid device id \n";
	exit(5);
}
return($value['floodlight']);
}

/*------------------------------------------------------------------------------*/
/* Main Routine 								*/
/*------------------------------------------------------------------------------*/
/*  https://dev.netatmo.com/apidocumentation/                                   */
/* Historic of rain data							*/
/*   Getting the historic of the rain data					*/
/*   (mean, min/max and min/max dates)						*/
/*------------------------------------------------------------------------------*/
global $DEBUG;
$env = getenv('NA_DEBUG');
if ( ( $env == 0) || $env == "false") 
{
	$DEBUG = 0;
}
if ( ( $env == 1) || $env == "true") 
{
	$DEBUG = 1;
	print " --------------------------------------------\n";
	print " DEBUG is ON \n";
	print " --------------------------------------------\n";
}

if (( $DEBUG == true)) { var_dump($argv); }
$argc = count($argv);
if (( $DEBUG == true)) {print " argc : ".$argc."\n";}

if ( ( $argc != 2 ) AND ( $argc != 4 ) AND ( $argc != 5 ) ) { usage(); exit(1); }
if ( $DEBUG == true )
{
	var_dump($argv);
}
if (isset($argc)) 
{
	if ( $argv[1] == "usage" )
	{
		usage();
		exit(1);
	}
	/* -------------------------------------------------------------------------------- */
	/*  activate or desactivate light on camera 					    */
	/* -------------------------------------------------------------------------------- */
	[$access_token, $refresh_token, $expire_token ] = get_token($grant_type,$client_id,$client_secret,$scope,$Content_Type);
	if ( $DEBUG == true )
	{
		echo " atoken ".$access_token."\n rtoken ".$refresh_token."\n etoken ".$expire_token."\n" ;
	}
	/* -------------------------------------------------------------------------------- */
	/*  activate or desactivate light on camera 					    */
	/* -------------------------------------------------------------------------------- */
	if ( $argv[1] == "homelist" )
	{
		get_homedata_forhome($access_token);
		exit(2);
	}
	$home_id = $argv[1];
	/* -------------------------------------------------------------------------------- */
	/*  activate or desactivate light on camera 					    */
	/* -------------------------------------------------------------------------------- */
	if ( ( $argv[2] == "light" ) AND ( ( $argv[3] == "on" ) ||  ($argv[3] == "off" ) || ( $argv[3] == "auto") || ($argv[3] == "list") || ($argv[3] == "current") ) )
    	{
		$light_action = $argv[3];
		if ( $DEBUG == true )
		{
			print " ----- light : ".$light_action."\n";
		}
		if ( $argv[3] == "list" )
		{
			[ $result2 ]  = get_homedata($access_token,$home_id);
			foreach ($result2 ['body']['homes'][0]['modules'] as $value)
			{
				if ( strpos($value['type'], 'NOC') !== false) 
				{
					print " ".$value['id']." ".$value['name']."\n";
				}
			}
		}
		else
		{
		if ( isset($argv[4]) )
			{
				if ( $argv[3] == "current" )
				{
					$id = $argv[4];
					print " Active light mode : ".get_light_mode($access_token,$home_id,$id)."\n";

				}
				else
				{
					$id = $argv[4];
					print " Light mode before: ".get_light_mode($access_token,$home_id,$id)."\n";
					set_light_mode($access_token,$home_id,$id,$light_action);
					print " Light mode after: ".get_light_mode($access_token,$home_id,$id)."\n";
				}
			}
		else
		{ 
			usage();
			exit(1);
		}
		}
	}
	else 
	{ 
		usage();
		exit(1);
	}
}
exit(0);

?>
