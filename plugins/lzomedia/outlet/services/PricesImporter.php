<?php


namespace LzoMedia\Outlet\Services;


use Kharanenka\Scope\ExternalIDField;
use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportProductModelFromCSV;
use Lovata\Shopaholic\Models\Product;
use LzoMedia\Outlet\Contracts\ImporterInterface;

class PricesImporter extends ImportOfferModelFromCSV
{

    use ExternalIDField;

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

        $this->prepareImportData();

        parent::import($this->getItem(), false);

        return $this;

    }


    /**
     * Prepare array of import data
     */
    protected function prepareImportData()
    {
        parent::setActiveField();

        $this->setProductField();

        parent::setQuantityField();

        parent::initPreviewImage();

        parent::initImageList();

        parent::prepareImportData();

    }


    public function setProductField(){

        $sProductID = array_get($this->arImportData, 'product_id');

        if ($sProductID === null) {
            return;
        }

        //Find product by external ID
        $obProduct = Product::withTrashed()->find($sProductID);
        if (empty($obProduct)) {
            $this->arImportData['product_id'] = null;
        } else {
            $this->arImportData['product_id'] = $obProduct->id;
        }

    }






}
