<?php

# acct, passwd, directory, mfg, mac, enabled
function getAccountDatabase()
{
    $arr = [
              ["<32 char account>", "<12 char secret>", "<directory>", "yealink", "<MAC>", "true"],
              ["<32 char account>", "<12 char secret>", "<directory>", "yealink", "000000000000", "true"],
           ];
    
    return $arr;
}

