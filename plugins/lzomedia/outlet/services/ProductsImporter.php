<?php


namespace LzoMedia\Outlet\Services;


use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportProductModelFromCSV;
use Lovata\Shopaholic\Models\Category;
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
     * @method
     * @return array
     */
    private function getItem()
    {
        return $this->item =[
            'name' => $this->json->name,
            'external_id' => $this->json->id,
            'code' => $this->json->offer->vendor->name->display,
            'active' => true,
            'category_id' => 2,
            'brand_id' => 1
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
        parent::setBrandField();
        $this->setCategoryField();

        parent::initPreviewImage();
        parent::initImageList();
        parent::initAdditionalCategoryField();

        parent::prepareImportData();
    }



    /**
     * @method setCategoryField
     * Set category_id filed value
     */
    protected function setCategoryField()
    {
        $sCategoryID = array_get($this->arImportData, 'category_id');
        if ($sCategoryID === null) {
            return;
        }

        //Find category by external ID
        $obCategory = Category::find($sCategoryID);
        if (empty($obCategory)) {
            $this->arImportData['category_id'] = null;
        } else {
            $this->arImportData['category_id'] = $obCategory->id;
        }
    }

}
