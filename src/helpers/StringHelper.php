<?php
/**
 * Created by PhpStorm.
 * User: oFix
 * Date: 019 19.10.18
 * Time: 15:43
 */

namespace ofixone\content\helpers;


class StringHelper extends \yii\helpers\StringHelper
{
    public static function mb_ucfirst($str, $encoding = NULL)
    {
        if ($encoding === NULL) {
            $encoding = mb_internal_encoding();
        }
        return mb_substr(mb_strtoupper($str, $encoding), 0, 1, $encoding) . mb_substr($str, 1, mb_strlen($str) - 1, $encoding);
    }

    public static function numWithWord($value, $words, $includeNumber = true)
    {
        $word = (
            $value % 10 == 1 && $value % 100 != 11 ?
                $words[0] :
                (
                    $value % 10 >= 2 && $value % 10 <= 4 && ($value % 100 < 10 || $value % 100 >= 20
                ) ? $words[1] : $words[2]));

        return $includeNumber ? $value . ' ' . $word : $word;
    }
}