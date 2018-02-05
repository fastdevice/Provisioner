<?php

# returns a valid MAC format or 0
function getMACfromFilename($filename)
{
    /*  
     *  Extract a potential MAC address from the filename, 
     *  not always expected to be a MAC address  
     */

    // strip first 12 chars from the filename
    $mac = strtolower(substr($filename, 0, 12)); 
    
    // Check if the 12 chars are formatted as a proper MAC address 
    if(isMacValid($mac))
    {
        return $mac;
    }
    return 0;
}

function isMacValid($mac)
{
  return (preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $mac) == 1);
}

function removeSeparatorA($mac, $separator = array(':', '-'))
{
  return str_replace($separator, '', $mac);
}

function getServerUserAgent()
{
    $req = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
    error_log ("Device: HUA String : " . $req . "\r\n", 3, './event.log');
    return explode(' ', $req);
}

# Expecting MAC in this format 00:00:00:00:00:00, returns 000000000000
function removeSeperatorsB($mac)
{
    $mac_array = explode(':', $mac);
    return strtolower(implode($mac_array));
}

function getFileExtension($file) 
{
   $ext_array = explode('.', $file);
   $extension = end($ext_array);
   return $extension ? $extension : false;
}

function openTextFileDownload($filename, $text)
{
    
    header('Content-type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename=' . $filename);
    
    # headers for html view
    echo '<html><head></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">';
    return $text;
   // echo '</pre></body></html>';
}

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str(
    $length,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

