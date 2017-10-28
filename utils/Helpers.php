<?php  

namespace Utils\Helpers;

function isRealString($str='')
{
    return (
        is_string($str)
        && strlen( trim($str) ) > 0
    );
}

function arrayFind($arr, $callback)
{
    foreach ($arr as $item) 
    {
        if( call_user_func($callback, $item) === TRUE ){
            return $item;
        }
    }

    return NULL;
}
