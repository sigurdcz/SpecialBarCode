<?php

namespace Sigurd\SpecialBarCode\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/app/code/Sigurd/SpecialBarCode/logs/log.log';
}

