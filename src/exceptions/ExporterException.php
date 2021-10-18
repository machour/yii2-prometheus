<?php

namespace machour\yii2\prometheus\exceptions;

use yii\base\Exception;

class ExporterException extends Exception
{
    public function getName()
    {
        return "Prometheus Exporter Exception";
    }
}