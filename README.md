# netatmo-floodlight
Management of floodlight for Netatmo external camera

The goal of this script is the management of floodlight of Neatmo external camera.
It is able to manage several "home" and several cameras per home.

# How to start 
1/ go the netatmo url: https://dev.netatmo.com/apps
   and create an application
   with the following scopes    read_presence write_presence access_presence
   note the values of  client_id,  home_id, access_token and refresh_token

2/ modify the script with your values. The variables using these values are at the beginning of the script
/*------------------------------------------------------------------------------*/
/* ONLY modify these FOUR variables with your data                              */
/*------------------------------------------------------------------------------*/
$client_id="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$client_secret="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

$access_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$refresh_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
/*------------------------------------------------------------------------------*/



# usage - the help
$ ./netatmo_light.php 
 ---------------------------------------------------------------------- 
 Usage: [usage|home id|homelist] <light> [auto|on|off|current|list] [list|device id] 
                                                                        
 usage                                                                  
          this help                                                     
                                                                        
 home id                                                                
          home id for an home. Use homelist first to get home id        
          000000000000000000000000 (example format)                     
                                                                        
 light                                                                  
          action will be applied on this device                         
                                                                        
 <light action>                                                         
          auto or on or off or current or list                                                                                                                                              
device id                                                              
          use list without <device id> first, to obtain device id       
          00:00:00:00:00:00 (example format)                            
 ---------------------------------------------------------------------- 
 
