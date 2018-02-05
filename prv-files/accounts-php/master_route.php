<?php

$app->get('/{acct}/{passwd}/{mfg}/{path}',
      function ($acct, $passwd, $mfg, $path) use ($app)
      {  
         $dir = valCredentials ($app->escape($acct), 
                                $app->escape($passwd), 
                                $app->escape($path));
         if (!$dir) {$app->abort(404);}
         
         $validate = valDeviceRequest ($app->escape($path), $app->escape($mfg));
         if (!$validate) {$app->abort(404);}

         $returnPath = findFolderPath ($app->escape($path), $app->escape($dir));
         if (!$returnPath) {$app->abort(404);}
         
         // If authorized, return config file
         return $app->sendFile($returnPath . $app->escape($path));
      }
); // end $app->get function
