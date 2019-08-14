<?php


namespace LzoMedia\Outlet\Services;


use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportProductModelFromCSV;
use Lovata\Shopaholic\Models\Category;
use LzoMedia\Outlet\Contracts\ImporterInterface;
use Illuminate\Support\Facades\Storage;

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
        return $this->item = [
            'name' => $this->json->name,
            'external_id' => $this->json->id,
            'code' => $this->json->offer->vendor->name->display,
            'preview_image' => $this->json->image->original,
            'images' => $this->json->image_gallery,
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

        $this->initPreviewImage();
        $this->initImageList();
        parent::initAdditionalCategoryField();

        parent::prepareImportData();
    }

    protected function initImageList()
    {
        if (!array_key_exists('images', $this->arImportData)) {
            $this->bNeedUpdateImageList = false;
            return;
        }

        $this->bNeedUpdateImageList = true;
        $ImageList = array_get($this->arImportData, 'images');
        array_forget($this->arImportData, 'images');

        if (empty($ImageList)) {
            return;
        }

        $this->arImageList = array();

        foreach ($ImageList as $idx => $img) {
            $im = $img->original;
            $extension = array_values(array_slice(explode(".", $im), -1))[0];

            $filename = array_get($this->arImportData, 'external_id') . '_' . $idx . '.' . $extension;

            $imgFP = storage_path('app/imgstorage/' . $filename);
            if (!file_exists($imgFP)) {
                Storage::put('imgstorage/' . $filename, file_get_contents($im));
            }
            $this->arImageList[$idx] = $imgFP;
        }
    }

    /**
     * Init preview image path
     */
    protected function initPreviewImage()
    {
        if (!array_key_exists('preview_image', $this->arImportData)) {
            $this->bNeedUpdatePreviewImage = false;
            return;
        }

        $this->bNeedUpdatePreviewImage = true;
        $this->sPreviewImage = array_get($this->arImportData, 'preview_image');
        if ($this->sPreviewImage == null) {
            return;
        }

        $extension = array_values(array_slice(explode(".", $this->sPreviewImage), -1))[0];
        $filename = array_get($this->arImportData, 'external_id') . '_preview.' . $extension;

        $imgFP = storage_path('app/imgstorage/' . $filename);

        if (!file_exists($imgFP)) {
            Storage::put('imgstorage/' . $filename, file_get_contents($this->sPreviewImage));
        }
        $this->sPreviewImage = $imgFP;
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
