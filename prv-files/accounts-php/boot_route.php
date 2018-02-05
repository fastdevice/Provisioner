<?php

$app->get('/{path}',
      function ($path) use ($app)
      {  
         
        $validate = valBootRequest ($app->escape($path));
         if (!$validate) {$app->abort(404);}    
    
         return $validate;
         
      }
); // end $app->get function
