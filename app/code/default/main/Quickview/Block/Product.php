<?php

class Chigusa_Quickview_Block_Product extends Mage_Catalog_Block_Product
{
    
    private $product;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('chigusa/quickview/product.phtml');
    }

    protected function _toHtml() {
        return parent::_toHtml();
    }
    
    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }
    
    public function getProduct() {
        return $this->product;
    }

    public function getFormAction()
    {
		return Mage::getUrl('tag/index/save', array(
            'product' => $this->product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl(),
            '_secure' => $this->_isSecure()
        ));
    }
	
	public function getAllowReview() {
		return Mage::getStoreConfig('catalog/review/allow_guest');
	}

    public function getCzAction()
    {
        return Mage::getUrl('review/product/post', array('id' => $this->product->getId(), '_secure' => $this->_isSecure()));
    }

    public function getRatings()
    {
        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
	

    /**
     * Retrieve list of gallery images
     *
     * @return array|Varien_Data_Collection
     */
    public function getGalleryImages()
    {
        //if ($this->_isGalleryDisabled) {
        //    return array();
        //}
        $collection = $this->getProduct()->getMediaGalleryImages();
        return $collection;
    }

    public function renderCloudOptions()
    {
        $output = "";
        $width = $this->getCloudConfig('zoomImage/zoomWidth');
        if (empty($width) || !is_numeric($width)) {
            $width = 'auto';
        }
        $height = $this->getCloudConfig('zoomImage/zoomHeight');
        if (empty($height) || !is_numeric($height)) {
            $height = 'auto';
        }

        $output .= "zoomWidth: '" . $width . "',";
        $output .= "zoomHeight: '" . $height . "',";
        $output .= "position: '" . $this->getCloudConfig('zoomImage/position') . "',";
        $output .= "smoothMove: " . (int) $this->getCloudConfig('zoomImage/smoothMove') . ",";
        $output .= "showTitle: " . ($this->getCloudConfig('zoomImage/showTitle') ? 'true' : 'false') . ",";
        $output .= "titleOpacity: " . (float) ($this->getCloudConfig('zoomImage/titleOpacity') / 100) . ",";

        $adjustX = (int) $this->getCloudConfig('zoomImage/adjustX');
        $adjustY = (int) $this->getCloudConfig('zoomImage/adjustY');
        if ($adjustX > 0) {
            $output .= "adjustX: " . $adjustX . ",";
        }
        if ($adjustY > 0) {
            $output .= "adjustY: " . $adjustY . ",";
        }

        $output .= "lensOpacity: " . (float) ($this->getCloudConfig('lens/lensOpacity') / 100) . ",";

        $tint = $this->getCloudConfig('originalImage/tint');
        if (!empty($tint)) {
            $output .= "tint: '" . $this->getCloudConfig('originalImage/tint') . "',";
        }
        $output .= "tintOpacity: " . (float) ($this->getCloudConfig('originalImage/tintOpacity') / 100) . ",";
        $output .= "softFocus: " . ($this->getCloudConfig('originalImage/softFocus') ? 'true' : 'false') . "";

        return $output;
    }

    public function renderLightboxOptions($options = 'lightbox')
    {
        $enableLightbox = (boolean) $this->getCloudConfig('zoomImage/enableLightbox');
        if ($enableLightbox) {
            return 'data-lightbox="' . $options . '"';
        }
        return '';
    }

    public function getCloudConfig($name)
    {
        return Mage::getStoreConfig('moo_cloudzoom/' . $name);
    }

    public function getCloudImage($product, $imageFile = null)
    {
        if ($imageFile !== null) {
            $imageFile = $imageFile->getFile();
        }
        $image = $this->helper('catalog/image')->init($product, 'image', $imageFile);

        $width = $this->getCloudConfig('originalImage/imageWidth');
        $height = $this->getCloudConfig('originalImage/imageHeight');

        if (!empty($width) && !empty($height)) {
            return $image->resize($width, $height);
        } else if (!empty($width)) {
            return $image->resize($width);
        } else if (!empty($height)) {
            return $image->resize($height);
        }
        return $image;
    }
	
	public function getSlideProductCollection() {
		$category = Mage::registry('current_category');
		$currentOrder = Mage::getBlockSingleton('catalog/product_list_toolbar')->getCurrentOrder();
		$currentDirection = Mage::getBlockSingleton('catalog/product_list_toolbar')->getCurrentDirection();
		
		$_productCollection = $category->getProductCollection()->addAttributeToSort($currentOrder, $currentDirection);

		return $_productCollection;
	}
}
