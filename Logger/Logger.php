<?php

namespace Sigurd\SpecialBarCode\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

class Logger extends MonologLogger implements LoggerInterface
{
    protected $scopeConfig;
    protected $enabled;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
                             $name,
        array                $handlers = [],
        array                $processors = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->enabled = (bool)$this->scopeConfig->getValue('specialbarcode/logging/enabled', ScopeInterface::SCOPE_STORE);

        parent::__construct($name, $handlers, $processors);
    }

    public function info($message = "", array $context = []): void
    {
        if ($this->enabled) {
            parent::info($message, $context);
        }
    }
}
