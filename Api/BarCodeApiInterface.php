<?php

namespace Sigurd\SpecialBarCode\Api;

interface BarCodeApiInterface
{
    /**
     * GET for Post api
     * @param string $attributeValue
     * @return string
     */
    public function update($attributeValue);
}
