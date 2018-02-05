<?php

include '/var/www/prv-files/common-php/helperlib_impl.php';
include '/var/www/prv-files/common-php/database_impl.php';

# Add Manufacturers here
include '/var/www/prv-files/common-php/mfg_yealink_impl.php';
include '/var/www/prv-files/common-php/mfg_polycom_impl.php';


# returns directory where config files can be found or 0 for unauthorized
function valCredentials ($acct, $passwd, $filename)
{
    
    # Check credentials: Account, Enabled, MAC, Password
    foreach (getAccountDatabase() as $value)
    {
       # Check if Account exists
       if (strcmp($acct, $value[0] ) === 0)
       {
          error_log ("\r\nAccount: Found : " . $value[0] . "\r\n", 3, './event.log');
        
          # Is Account enabled
          if ($value[5] === "true")
          {
                error_log ("Account: Enabled \r\n", 3, './event.log');

                # Check if MAC address is set to ANY allowed or specific MAC required
                if (strcmp("000000000000", $value[4] ) !== 0)
                {
                   # Look for specific MAC address
                   $mac = getMACfromFilename($filename);
                     
                   # First check if the filename is a valid MAC address
                   if ($mac !== 0)
                   {
                      error_log ("Account: Valid Format : " . $mac . "\r\n", 3, './event.log');

                      #Check for MAC in our database
                      if (strcmp($mac, $value[4] ) !== 0 )
                      {
                         error_log ("Account: MAC Mismatch : " . $value[4] . "\r\n", 3, './event.log');
                         error_log ("Account: Status : Unauthorized \r\n", 3, './event.log');
                         continue; // MAC not found in database, look for next MAC address
                      }
                    }
                }

                # Check if Password is correct
                if (strcmp($passwd, $value[1] ) === 0)
                {
                    error_log ("Account: Directory : " . $value[2] . "\r\n", 3, './event.log');
                    error_log ("Account: Status : Authorized \r\n", 3, './event.log');
                    $dir = $value[2]; // assign directory
                    unset($value);
                    return $dir; // authorized return directory path
                }

                error_log ("Account: Unauthorized  \r\n", 3, './event.log');
                unset($value);
                return 0; // unauthorized
          } // account enabled
          error_log ("Account: MAC Disabled : " . $value[4] . "\r\n", 3, './event.log');
       } // account exists
    } // foreach
    
    error_log ("Account: End of Search : no authorized event \r\n", 3, './event.log');
    unset($value);
    return 0; // unauthorized
}

# Returns true or false for authorization of device
function valDeviceRequest ($filename, $mfg)
{  
    
    $debug = false;
    if($debug) {return true;} // debug force authorized request
    $validated = false; // assume all requests are unauthorized
    
    error_log ("\r\nDevice: Timestamp : " . date("Y-m-d") . " " . date("h:i:sa") . "\r\n", 3, './event.log');
    error_log ("Device: Filename : " . $filename . "\r\n", 3, './event.log');
    
    switch ($mfg) {

        case "yealink":
            error_log ("Device: Requesting : Yealink" . "\r\n", 3, './event.log');
            $validated = valYealink($filename);
            break;

        case "polycom":
            error_log ("Requesting: Polycom" . "\r\n", 3, './event.log');
           # $validated = valPolycom($filename);
            break;            
    
        case "mozilla":
            error_log ("Requesting: Mozilla/5.0" . "\r\n", 3, './event.log');
           # $validated = valMozilla($filename);
            break;

        default:
           error_log ("Unreckognized client info." . "\r\n", 3, './event.log');
           return false;
       
    }
    
    # Return validation of request 
    if ($validated)
    {
      error_log ("Device: Status : Authorized" . "\r\n", 3, './event.log');
      return true;
    } 
    else
    { 
      error_log ("Device: Status : Unauthorized" . "\r\n", 3, './event.log');
      return false; 
    }
}

function valBootRequest($filename)
{
    // connectDatabase();
    $mac = getMACfromFilename($filename);
    if ((!$mac) || (strcmp($mac, "000000000000") === 0))
    {
        error_log ("Boot: Invalid MAC : " . $filename . "\r\n", 3, './event.log');
        return false;
    }
    
    foreach (getAccountDatabase() as $value)
    { 
       # Check if MAC exists
       if (strcmp($mac, $value[4] ) === 0)
       {
          error_log ("Boot: MAC : " . $value[4] . "\r\n", 3, './event.log');
          error_log ("Boot: Account : " . $value[0] . "\r\n", 3, './event.log');
        
          # Is Account enabled
          if ($value[5] === "true")
          {
              error_log ("Boot: Account : Enabled \r\n", 3, './event.log');
              switch ($value[3]) {
                 
                 case "yealink":
                     error_log ("Boot: Requesting : Yealink" . "\r\n", 3, './event.log');      
                     //valYealinkBoot($fileExt, $mac, $acct, $secret)
                     $bootFile = valYealinkBoot($filename, $value[4], $value[0], $value[1]);
                     break;
                     
                 case "polycom":
                     error_log ("Boot: Requesting: Polycom" . "\r\n", 3, './event.log');
                     
                     break;            
    
                case "mozilla":
                    error_log ("Boot: Requesting: Mozilla/5.0" . "\r\n", 3, './event.log');
                    
                    break;

                default:
                    error_log ("Unreckognized client info." . "\r\n", 3, './event.log');
                    return false;
              } // end switch
              
              # Return boot file for device 
              if ($bootFile)
              {
                 error_log ("Boot: Status : Authorized" . "\r\n", 3, './event.log');
                 return $bootFile;
              } 
              else
              { 
                 error_log ("Boot: Status : Unauthorized" . "\r\n", 3, './event.log');
                 return false; 
              }
          } // account enabled
          error_log ("Boot: MAC Disabled : " . $value[4] . "\r\n", 3, './event.log');
       } // account exists
    } // foreach
    
    error_log ("Boot: End of Search : no authorized MAC \r\n", 3, './event.log');
    unset($value);
    return false; // unauthorized
}

