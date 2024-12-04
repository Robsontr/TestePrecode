<?php
class Products
{

    private $id;
    private $sku;
    private $name;
    private $shortName;
    private $description;
    private $status;
    private $wordKeys;
    private $price;
    private $promotionalPrice;
    private $cost;
    private $weight;
    private $width;
    private $height;
    private $length;
    private $brand;
    private $nbm;
    private $model;
    private $gender;
    private $volumes;
    private $warrantyTime;
    private $category;
    private $subcategory;
    private $endcategory;
    private $urlYoutube;
    private $googleDescription;
    private $manufacturing;

    public function __construct(
        $name,
        $description,
        $status,
        $price,
        $promotionalPrice,
        $cost,
        $weight,
        $width,
        $height,
        $length,
        $brand
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->price = $price;
        $this->promotionalPrice = $promotionalPrice;
        $this->cost = $cost;
        $this->weight = $weight;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->brand = $brand;
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

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    public function getShortName()
    {
        return $this->shortName;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setWordKeys($wordKeys)
    {
        $this->wordKeys = $wordKeys;
    }

    public function getWordKeys()
    {
        return $this->wordKeys;
    }


    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPromotionalPrice($promotionalPrice)
    {
        $this->promotionalPrice = $promotionalPrice;
    }

    public function getPromotionalPrice()
    {
        return $this->promotionalPrice;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setNbm($nbm)
    {
        $this->nbm = $nbm;
    }

    public function getNbm()
    {
        return $this->nbm;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setVolumes($volumes)
    {
        $this->volumes = $volumes;
    }

    public function getVolumes()
    {
        return $this->volumes;
    }

    public function setWarrantyTime($warrantyTime)
    {
        $this->warrantyTime = $warrantyTime;
    }

    public function getWarrantyTime()
    {
        return $this->warrantyTime;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;
    }

    public function getSubcategory()
    {
        return $this->subcategory;
    }

    public function setEndcategory($endcategory)
    {
        $this->endcategory = $endcategory;
    }

    public function getEndcategory()
    {
        return $this->endcategory;
    }

    public function setUrlYoutube($urlYoutube)
    {
        $this->urlYoutube = $urlYoutube;
    }

    public function getUrlYoutube()
    {
        return $this->urlYoutube;
    }

    public function setGoogleDescription($googleDescription)
    {
        $this->googleDescription = $googleDescription;
    }

    public function getGoogleDescription()
    {
        return $this->googleDescription;
    }

    public function setManufacturing($manufacturing)
    {
        $this->manufacturing = $manufacturing;
    }

    public function getManufacturing()
    {
        return $this->manufacturing;
    }
}
