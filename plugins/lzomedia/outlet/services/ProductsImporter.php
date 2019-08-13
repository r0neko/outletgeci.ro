<?php


namespace LzoMedia\Outlet\Services;


use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportProductModelFromCSV;
use LzoMedia\Outlet\Contracts\ImporterInterface;

class ProductsImporter extends ImportProductModelFromCSV
{

    public $json;

    private $item;

    public $obModel;

    public function __construct($json)
    {

        $this->json = $json;

    }

    /**
     * @return array
     */
    private function getItem()
    {
        return $this->item =[
            'name' => $this->json->name,
            'external_id' => $this->json->id,
            'code' => $this->json->offer->vendor->name->display,
            'active' => true,
            'category_id' => [1],
            'brand_id' => [1]
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
