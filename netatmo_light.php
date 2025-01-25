#!/usr/bin/php
<?php
/*------------------------------------------------------------------------------*/
/* 2025/01/25 (c) Phil353556 github.com                                         */
/*  email: 51y9oj579@relay.firefox.com                                          */
/*                                                                              */
/* This piece of software is as IS with no Warranty. Use it of your own risk!   */
/* See LICENCE.md file in repository                                            */
/*------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------*/
/* The goal is to manage floodlight camera from Netatmo 			*/
/* Run the script without any parameter to display the usage                    */
/*------------------------------------------------------------------------------*/
/* list of function used                                                        */
/*                                                                              */
/* function f_get_homedata($access_token,$home_id)				*/
/* function f_get_homedata_forhome($access_token)				*/
/* function f_get_homestatus_NOC($access_token,$home_id)			*/
/* function f_usage()								*/
/* function f_set_light_mode($access_token,$home_id,$id,$state)			*/
/* function f_get_light_mode($access_token,$home_id,$id)			*/
/*------------------------------------------------------------------------------
Return codes by function:

function f_get_homedata($access_token,$home_id)
   10 : No camera available for this home

function f_get_homedata_forhome($access_token)	
   20 : Error in general an issue with the access_token

function f_get_homestatus_NOC($access_token,$home_id)
   30 : Error in general an issue with the access_token
   31 : No camera available for this home

function f_set_light_mode($access_token,$home_id,$id,$state)	
   40 : Error from the server for this action requested

function f_get_light_mode($access_token,$home_id,$id)			
   50 : Invalid device ID

function f_usage()					
    -> no return code

Main routine
   101 : usage was the first parameter
   102 : the file file_access_token.txt cannot be open
   103 : homelist was the first parameter 
   104 : number of parameters incorrect
   105 : parameters not valids

/* --------------------------------------------------------------------------- */
/* Get the list of external camera for this home                               */
/* --------------------------------------------------------------------------- */
function f_get_homedata($access_token,$home_id)
{
global $DEBUG;
if ( $DEBUG == true )
{	
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" function: f_get_homedata							     \n");
	printf(" ----------------------------------------------------------------------------------- \n");
}	

$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homesdata?home_id=$home_id&device_types=NOC",
        CURLOPT_RETURNTRANSFER => true
        )
);

if ( $DEBUG == true )
{
	curl_setopt($handle, CURLOPT_VERBOSE, false);
}

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
	printf("NO CAMERA\n");
	exit(10);
}

return [$array];
}

/* --------------------------------------------------------------------------- */
/* Get list of available home                                                  */
/* --------------------------------------------------------------------------- */
function f_get_homedata_forhome($access_token)
{
global $DEBUG;
printf(" ----------------------------------------------------------------------------------- \n");
printf(" ".date('Y-m-d H:i:s\Z',time())." (UTC)                                 \n\n");

if ( $DEBUG == true )
{	
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" function: f_get_homedata_forhome						     \n");
	printf(" ----------------------------------------------------------------------------------- \n");
}	


$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homesdata",
        CURLOPT_RETURNTRANSFER => true
        )
);

if ( $DEBUG == true )
{
	curl_setopt($handle, CURLOPT_VERBOSE, false);
}

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

if ( isset($array['error']) )
	{
		printf(" ERROR ".$array['error']['message']."\n");
		exit(20);
	}


if (isset ($array['body']['homes']) )
{
	foreach ($array['body']['homes'] as $value)
	{
		printf(" ".$value['id']." ".$value['name']."\n");
	}
}

return [$array];
}

/* --------------------------------------------------------------------------- */
/* Get all data for this home                                                  */
/* --------------------------------------------------------------------------- */
function f_get_homestatus_NOC($access_token,$home_id)
{
global $DEBUG;
if ( $DEBUG == true )
{
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" function: f_get_homestatus_NOC  						     \n");
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" access token: -".$access_token."-                                                   \n");
}


$handle = curl_init();
curl_setopt_array($handle, array(
        CURLOPT_URL => "https://api.netatmo.com/api/homestatus?home_id=$home_id&device_types=NOC",
        CURLOPT_RETURNTRANSFER => true
        )
);

if ( $DEBUG == true )
{
	curl_setopt($handle, CURLOPT_VERBOSE, true);
}

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
if ( isset($array['error']) )
	{
		printf(" ERROR ".$array['error']['message']."\n");
		exit(30);
	}

if (!isset ($array['body']))
{
	printf("NO CAMERA\n");
	exit(31);
}

return [$array];
}

/*------------------------------------------------------------------------------*/
/* Action on external foodlight                                                 */
/*------------------------------------------------------------------------------*/
function f_set_light_mode($access_token,$home_id,$id,$state)
{
global $DEBUG;
if ( $DEBUG == true )
{
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" function: f_set_light_mode      						     \n");
	printf(" ----------------------------------------------------------------------------------- \n");
}

$handle = curl_init();

$datas = '{"home":{"id":"'.$home_id.'","modules":[{"id":"'.$id.'","floodlight":"'.$state.'"}]}}';

curl_setopt_array($handle, array(
  CURLOPT_URL => 'https://api.netatmo.com/api/setstate',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $datas,
));
curl_setopt($handle, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: Bearer ' . $access_token
	)
);
$response = curl_exec($handle);
curl_close($handle);

if ( $DEBUG == true )
{
	var_dump($response);
}

if ( is_string($response) )
{
	if ( (str_contains($response, 'ok')) AND (str_contains($response, 'time_server')) )  {
		$pos1 = strpos($response,',');
		$sub1=substr($response, $pos1);
		$pos2 = strpos($sub1,':');
		$sub2=substr($sub1, $pos2+1,10);
		printf(" OK from server: ".date('Y-m-d H:i:s\Z',$sub2)." \n");
	}
	else
	{
		printf(" ----------------------------------------------------------------------------------- \n");
		printf(" ERROR from server: \n");
		var_dump($response);
		exit(40);
	}
}

}

/*------------------------------------------------------------------------------*/
/* Get the current mode for the external camera in parameter                    */
/*------------------------------------------------------------------------------*/
function f_get_light_mode($access_token,$home_id,$id)
{
global $DEBUG;
[ $result ] = f_get_homestatus_NOC($access_token,$home_id);  
if ( $DEBUG == true )
{	
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" function: f_get_light_mode      						     \n");
	printf(" ----------------------------------------------------------------------------------- \n");
}	
$found = 0;

foreach ($result ['body']['home']['modules'] as $value)
{
	if ( $DEBUG == true )
	{
		printf(" Light : ".$value['floodlight']."\n");
		printf(" id : ".$value['id']."\n");
		printf(" id cli : ".$id."\n");
	}
	$pos = strpos($value['type'],'NOC');
	if  ( $pos == 0 )
	{
		if ( $DEBUG == true) 
		{
		       printf(" Light : ".$value['floodlight']."\n");
		}
		if ( $id == $value['id'] )
		{
			 $found = 1;
		}

		if ( $DEBUG == true )
		{
			printf(" Camera : ".$value['type']."\n");
			printf(" firmware : ".$value['firmware_revision']."\n");
			printf(" id : ".$value['id']."\n");
			printf(" Wifi state : ".$value['wifi_state']."\n");
			printf(" Wifi quality : ".$value['wifi_strength']."\n");
		}
	}
   if ( $found == 1 ) 
   {
	   break;
   }
}
if ( $found == 0 ) 
{
	printf(" ----------------------------------------------------------------------------------- \n");
	printf(" ERROR Invalid device id 							     \n");
	exit(50);
}

return($value['floodlight']);
}

/*------------------------------------------------------------------------------*/
/* Display the help for this script                                             */
/*------------------------------------------------------------------------------*/
function f_usage()
{
printf(" ----------------------------------------------------------------------------------- \n");
printf(" ".date('Y-m-d H:i:s\Z',time())." (UTC)                                 \n");
printf("                                                                        \n");
printf(" usage                                                                  \n");
printf("          this help                                                     \n");
printf("                                                                        \n");
printf(" home id                                                                \n");
printf("          home id for an home. Use homelist first to get home id        \n");
printf("          000000000000000000000000 (example format)                     \n"); 
printf("                                                                        \n");
printf(" light                                                                  \n");
printf("          action will be applied on this device                         \n");
printf("                                                                        \n");
printf(" <light action>                                                         \n");
printf("          auto or on or off or current or list, use list first          \n");
printf("                                                                        \n");
printf(" device id                                                              \n");
printf("          use list without <device id> first, to obtain device id       \n");
printf("          00:00:00:00:00:00 (example format)                            \n");
printf("                                                                        \n");
printf(" Examples:                                                              \n");
printf(" ./netatmo_light.php homelist                                           \n");
printf(" ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light list                 \n");
printf(" ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light current 7x:xx:xx:xx:xx:x4 \n");
printf(" ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light on  7x:xx:xx:xx:xx:x4\n");
printf(" ----------------------------------------------------------------------------------- \n");
}

/*------------------------------------------------------------------------------*/
/* Main Routine 								*/
/*------------------------------------------------------------------------------*/
/*  https://dev.netatmo.com/apidocumentation/                                   */
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
	printf(" ----------------------------------------------------------------------------------- \n");
        printf(" DEBUG is ON                                                                         \n");
        printf(" ----------------------------------------------------------------------------------- \n");
}

if (( $DEBUG == true)) { var_dump($argv); }
$argc = count($argv);
if (( $DEBUG == true)) {printf(" argc : ".$argc."\n");}

if ( ( $argc != 2 ) AND ( $argc != 4 ) AND ( $argc != 5 ) ) { f_usage(); exit(1); }
if ( $DEBUG == true )
{
	var_dump($argv);
}
if (isset($argc)) 
{
	if ( $argv[1] == "usage" )
	{
		f_usage();
		exit(101);
	}
	/* -------------------------------------------------------------------------------- */
	/*  Retrieve access token store in the file 					    */
	/* -------------------------------------------------------------------------------- */
	$file_access_token="file_access_token.txt";

	if ( $DEBUG == true )
	{
		printf(" Retrieve access token store in file : ".$file_access_token." \n");
	}

	if (!$myfile = @fopen($file_access_token, 'r')) {
        	printf(" The file cannot be open: $file_access_token \n");
        	exit(102);
	}
	$access_token=str_replace("\n","",fread($myfile,filesize($file_access_token)));
	fclose($myfile);

	/* -------------------------------------------------------------------------------- */
	/*  get all the home id for this account    					    */
	/* -------------------------------------------------------------------------------- */
	if ( $argv[1] == "homelist" )
	{
		f_get_homedata_forhome($access_token);
		printf(" ----------------------------------------------------------------------------------- \n");
		exit(103);
	}
	$home_id = $argv[1];
	/* -------------------------------------------------------------------------------- */
	/*  activate,desactivate, list and current state for the floodlight on camera       */
	/* -------------------------------------------------------------------------------- */
	if ( ( $argv[2] == "light" ) AND ( ( $argv[3] == "on" ) ||  ($argv[3] == "off" ) || ( $argv[3] == "auto") || ($argv[3] == "list") || ($argv[3] == "current") ) )
    	{
		$light_action = $argv[3];
		if ( $DEBUG == true )
		{
			printf(" ----- light : ".$light_action."\n");
		}
		if ( $argv[3] == "list" )
		{
			printf(" ----------------------------------------------------------------------------------- \n");
			printf(" ".date('Y-m-d H:i:s\Z',time())." (UTC)                                 \n\n");
			[ $result2 ] = f_get_homedata($access_token,$home_id);
		
			foreach ($result2 ['body']['homes'][0]['modules'] as $value)
			{
				if ( $DEBUG == true )
				{
					var_dump($value);
				}

				if ( strpos($value['type'], 'NOC') !== false ) 
				{
					printf(" ".$value['id']." ".$value['name']."\n");
				}
			}
			
			printf(" ----------------------------------------------------------------------------------- \n");
		}
		else
		{
		if ( isset($argv[4]) )
			{
				if ( $argv[3] == "current" )
				{
					$id = $argv[4];
					printf(" ----------------------------------------------------------------------------------- \n");
					printf(" ".date('Y-m-d H:i:s\Z',time())." (UTC)                                              \n\n");
					printf(" Active light mode : ".f_get_light_mode($access_token,$home_id,$id)."                \n");
					printf(" ----------------------------------------------------------------------------------- \n");

				}
				
				if ( ($argv[3] == "on") || ($argv[3] == "off") || ($argv[3] == "auto") )
				{
					printf(" ----------------------------------------------------------------------------------- \n");
					printf(" ".date('Y-m-d H:i:s\Z',time())." (UTC)                                              \n\n");
					$id = $argv[4];
					printf(" Light mode before: ".f_get_light_mode($access_token,$home_id,$id)."                 \n");
					f_set_light_mode($access_token,$home_id,$id,$light_action);
					printf(" Light mode after: ".f_get_light_mode($access_token,$home_id,$id)."                  \n");
					printf(" ----------------------------------------------------------------------------------- \n");
				}
			}
		else
		{ 
			f_usage();
			exit(104);
		}
		}
	}
	else 
	{ 
		f_usage();
		exit(105);
	}
}
exit(0);

?>
