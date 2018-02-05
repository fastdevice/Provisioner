<?php

function findFolderPath ($path, $acct_10did)
{  
       
    $path_accts = "/var/www/prv-files/accounts/" . $acct_10did;
    $path_boot = $path_accts . "/yealink/boot/";
    $path_config = $path_accts . "/yealink/config/";
    $path_unauth = $path_accts . "/yealink/unauth/";
    
    if (!file_exists($path_boot .  $path))
    {
          if (!file_exists($path_config . $path))
          {
             if (!file_exists($path_unauth . $path))
             { 
                error_log ("Path: File not found" . "\r\n", 3, './event.log');
                return false;
             }
             error_log ("Path: Unauth : Found" . "\r\n", 3, './event.log');
             return "$path_unauth";   
          }
          error_log ("Path: Config : Found" . "\r\n", 3, './event.log');
          return "$path_config";
    }
    error_log ("Path: Boot : Found" . "\r\n", 3, './event.log');
    return "$path_boot";       
}
