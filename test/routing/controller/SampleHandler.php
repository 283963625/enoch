<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/25
 * Time: 15:59
 */

namespace sinri\enoch\test\routing\controller;


use sinri\enoch\mvc\ApiInterface;

class SampleHandler extends ApiInterface
{
    public function handleCommonRequest()
    {
        echo __METHOD__;
    }

    public function handleErrorRequest()
    {
        echo __METHOD__;
    }
}