<?php


namespace LzoMedia\Outlet\Services;


use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportProductModelFromCSV;
use LzoMedia\Outlet\Contracts\ImporterInterface;

class PricesImporter extends ImportOfferModelFromCSV
{

    public $json;

    public $product;

    public function __construct()
    {

    }

    /**
     * @param $json
     */
    public function setJson($json){

        $this->json = $json;
    }

    /**
     * @param $product
     */
    public function setProduct($product){
        $this->product = $product;
    }

    /**
     * @return array
     */
    private function getItem()
    {

        return [
            'product_id' => $this->product->id,
            'external_id' =>  $this->product->id,
            'name' => $this->json->name,
            'active' => true,
            'code' => $this->json->part_number_key,
            'price' => $this->json->offer->price->current,
            'old_price' => $this->json->offer->price->initial,
            'quantity' => 1
        ];
    }

    /**
     * @throws \Throwable
     */
    public function process(){


        parent::import($this->getItem(), false);


        return $this;

    }





}
