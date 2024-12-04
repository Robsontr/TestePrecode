<?php
class CartDTO
{

    private $id;
    private $sku;
    private $price;
    private $qty;

    public function __construct(
        $sku,
        $price,
        $qty
    ) {
        $this->sku = $sku;
        $this->price = $price;
        $this->qty = $qty;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    public function getQty()
    {
        return $this->qty;
    }
}
