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
$client_id="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$client_secret="xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$access_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$refresh_token = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxx|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

3/ run the script with homelist option
  $ ./netatmo_floodlight.php homelist
  a list of your home  (ie home_id) you have access to is display
  then
  use the value of one home and move forward with light list options 

  $ ./netatmo_floodlight.php 000000000000000000000000 light list 
  all cameras for this home are displayed 
  pick the value for one camera and move forward with the action you want to apply for this floodlight

  $ ./netatmo_floodlight.php 000000000000000000000000 light current  00:00:00:00:00:00

  current: display the active mode for this camera
  auto:  switch the mode for this camera to auto
  on  : switch the floodlight of this camera ON
  off  : switch the floodlight of this camera OFF
  list : list all camera (ie all device id) for this home

3/ run the script with no option, the help is displayed

# Return codes 

0 : ok                                                                       /
1 : no option or option is usage or invalid option                           
2 : exit following homelist option                                           
3 : no external camera for this home in function get_homedata_forhome        
4 : no external camera detected in function get_homestatus_NOC               
5 : Invalid device id in function get_light_mode                             
6 : error doing action in function in function set_light                     
7 : invalid client or invalid grant in function get_token: check contents of variable


# usage - the help
$ ./netatmo_floodlight.php 
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
 
# Debug mode
if you want to activate the debug mode 
export  NA_DEBUG=1 
or
export NA_DEBUG=true

To desactivate the debug mode
export  NA_DEBUG=0
or
export NA_DEBUG=false
