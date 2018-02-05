<?php

function valYealink ($filename)
{
    
    $hua = getServerUserAgent();
    
    error_log ("Yealink: Requestor: " . $hua[0] . "\r\n", 3, './event.log');

    # [0] = 'Yealink', [1] = SIP-xxx, [2] = firmware version, [3] = '00:00:00:00:00:00'
    
    # Get the MAC address from the HUA
    $mac = removeSeperatorsB($hua[3]);
    error_log ("Yealink: MAC HUA: " . $mac . "\r\n", 3, './event.log');
    
    # Extract a potential MAC address from the path filename
    $mac_path = getMACfromFilename($filename);
     
    if (strcmp ($hua[0], 'Yealink') === 0 ) 
    {
       # Check if the file request is valid
       if (isFileValid_yl($filename, $mac, $mac_path))
       {
            return true;
       }
    }
    return false;
}

function isFileValid_yl($filename, $mac, $mac_path)
{
  # Valid filenames allowed
  $arr = ["common.cfg", "y000000000000.boot"];
  
  # First check if the path is a valid MAC address
  if (isMacValid($mac_path))
  {
    error_log ("Yealink: MAC PATH: " . $mac_path . "\r\n", 3, './event.log');
    if (strcmp($mac, $mac_path ) === 0 )
    {
      return true;
    }
  }
  
  # Second check if the filename is a Yealink valid request
  foreach ($arr as $value)
  {
     if (strcmp($filename, $value ) === 0)
     {
        unset($value);
        return true;
     }
  } 
  
  # none of the above, so invalid request
  return false;
}

function bootString_yl($mac)
{
       
    $text = "#!version:1.0.0.1"
            ."\n\n"
            . "include:config <" . $mac . ".cfg>"
            ."\n\n"
            . "overwrite_mode = 1"
            ."\n";
    
    return openTextFileDownload($mac.".boot", $text);
 
}

function prvString_yl($mac, $acct, $secret)
{
    
    $text = "#!version:1.0.0.1"
            ."\n\n"
            . "auto_provision.server.url = http://<your url>/" . $acct . '/' . $secret . '/' . "yealink" . '/'
            ."\n";
    
    error_log ("http://<your url>/". $acct. '/' . $secret . '/'. "yealink" . '/'. "\r\n", 3, './event.log');
    
    return openTextFileDownload($mac.".cfg", $text);
    
}

function valYealinkBoot($filename, $mac, $acct, $secret)
{
    
    $debug = false;
    if(!$debug) { // debug force authorized request
        # Check if the file is valid
        if (!valYealink($filename))
        {
             return false;
        }
    }
    
    $fileExt = getFileExtension($filename);
    
    if (strcmp($fileExt, "boot") === 0)
    {
        error_log ("Boot: BOOT : " . $mac . "\r\n", 3, './event.log'); 
        # Return the boot file to Device
        return bootString_yl($mac);
    }
    
    if (strcmp($fileExt, "cfg") === 0)
    {
        error_log ("Boot: CFG : " . $mac . "\r\n", 3, './event.log');
        # Return the Config file to the device
        return prvString_yl($mac, $acct, $secret);
    }
    return false;
}
