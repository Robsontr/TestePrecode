<?php

class ItensDTO
{
    private $sku;
    private $valorUnit;
    private $quantidade;

    public function __construct($sku, $valorUnit, $quantidade)
    {
        $this->sku = $sku;
        $this->valorUnit = $valorUnit;
        $this->quantidade = $quantidade;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setValorUnit($valorUnit)
    {
        $this->valorUnit = $valorUnit;
    }


    public function getvalorUnit()
    {
        return $this->valorUnit;
    }

    public function setQuantidade($quantidade)
    {
        return $this->quantidade = $quantidade;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }
}
