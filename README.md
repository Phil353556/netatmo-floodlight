# netatmo-floodlight
Management of floodlight for Netatmo external camera

The goal of this script is the management of floodlight of Neatmo external camera.
It is able to manage several "home" and several cameras per home.

# usage
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
