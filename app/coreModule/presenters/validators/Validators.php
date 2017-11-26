<?php

namespace App\CoreModule\Presenters\Validators;

class MyValidators
{
    //validace formuláře - tel. číslo
     public static function telnumberValidator($telnumber) {     
                return \preg_match('/^\+420[1-9][0-9]{8}$/',$telnumber->getValue());
                 
     }
     //validace formuláře - URL kontaktu
     public static function urlValidator($url) {
                return \preg_match('/^[a-z]+\-[a-z]+\-[0-9]+/',$url->getValue());
     }
     
      //validace formuláře - celého jmena 
     public static function fullnameValidator($fullname) {
                return \preg_match('/^[a-žA-Ž]{2,16}\s[a-žA-Ž]{2,32}/',$fullname->getValue());
     }
     

}