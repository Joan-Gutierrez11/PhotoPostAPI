<?php

namespace App\Utils;


/**
 * 
 */
trait PathUrlGetter
{
    protected function getUrl($value){
        if(!$value)
            return null;

        return (env('APP_DEBUG'))
            ? str_replace(env('APP_URL'), env('APP_URL').':8000', \Storage::url($value))
            : \Storage::url($value);
    }
}
