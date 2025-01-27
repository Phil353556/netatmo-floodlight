# netatmo-floodlight
Management of floodlight for Netatmo external camera

The goal of this script is the management of floodlight of Neatmo external camera.
It is able to manage several "home" and several cameras per home.

# Pre-requisistes
The following packages must be already available or installed on the system before using the php script supplied
* php (mini version 8)
* php-curl

# How to start 
1/ Use the netatmo_manage_tokens.php script from the same user. It will allow you to manage access_token and refresh token
   
2/ Run the script with homelist option
  $ **./netatmo_floodlight.php homelist**
  a list of your home  (ie home_id) you have access to is display
  then
  use the value of one home and move forward with light list options 

  $ **./netatmo_floodlight.php 000000000000000000000000 light list **
  all cameras for this home are displayed 
  pick the value for one camera and move forward with the action you want to apply for this floodlight
  
  replace 000000000000000000000000 and 00:00:00:00:00:00  with the values obtained,

  $ **./netatmo_floodlight.php 000000000000000000000000 light current  00:00:00:00:00:00**

  current: display the active mode for this camera
  auto:  switch the mode for this camera to auto
  on  : switch the floodlight of this camera ON
  off  : switch the floodlight of this camera OFF
  list : list all camera (ie all device id) for this home

3/ Run the script with no option, the help is displayed

# usage - the help

`$ ./netatmo_floodlight.php  
  -----------------------------------------------------------------------------------  
 2025-01-25 17:11:54Z (UTC)                                 
                                                                        
 usage                                                                  
          this help                                                     
                                                                        
 home id                                                                
          home id for an home. Use homelist first to get home id        
          000000000000000000000000 (example format)                     
                                                                        
 light                                                                  
          action will be applied on this device                         
                                                                        
 <light action>                                                         
          auto or on or off or current or list, use list first          
                                                                        
 device id                                                              
          use list without <device id> first, to obtain device id       
          00:00:00:00:00:00 (example format)                            
                                                                        
 Examples:                                                              
 ./netatmo_light.php homelist                                           
 ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light list                 
 ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light current 7x:xx:xx:xx:xx:x4   
 ./netatmo_light.php 5xxxxxxxxxxxxxxxxxxxxxc light on  7x:xx:xx:xx:xx:x4  
 -----------------------------------------------------------------------------------   `

# Examples

![image](https://github.com/user-attachments/assets/db2f7a07-2f1a-4d9d-a4ce-4bd560d8e679)

![image](https://github.com/user-attachments/assets/54c410b5-bb13-431c-a691-a62035f05452)

![image](https://github.com/user-attachments/assets/da18a3a5-690f-45d1-9241-1e7a1d4cb2a8)

![image](https://github.com/user-attachments/assets/54df67a3-110a-42db-92df-8817a39fa2cd)
  
 # Return codes by function 

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

# Debug mode  
if you want to activate the debug mode   
export  NA_DEBUG=1   
or  
export NA_DEBUG=true  

To desactivate the debug mode  
export  NA_DEBUG=0  
or  
export NA_DEBUG=false  

# Troubleshooting   

 * The rights for the script must be at minimum 'execute' for the user, using the command:  
chmod u+x ./netatmo_floodlight.php  
 * if the following error message is displayed: 
    "PHP Fatal error:  Uncaught Error: Call to undefined function str_contains() "
   it means the version of php is not at least version 8, needed for the function str_contains


