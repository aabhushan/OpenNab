<?php

/*
 * @author Aabhushan Mainali
 */

class utils{

    /* 
     * Generates random string of given $length
     */
    public static function randomString($length) {
        
        $ranStr ='';    
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        for ($loop = 0; $loop < $length; $loop++) {
            $ranStr .= $characters[mt_rand(0, strlen($characters))];
        }

        return $ranStr;
    }

}

?>
