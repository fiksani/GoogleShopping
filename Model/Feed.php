<?php

namespace Fandi\GoogleShopping\Model;

class Feed
{
    /**
     * General Helper
     *
     * @var \Fandi\GoogleShopping\Helper\Data
     */
    private $_helper;

    /**
     * Product Helper
     *
     * @var \Fandi\GoogleShopping\Helper\Products
     */
    private $_productFeedHelper;

    /**
     * Store Manager
     *
     * @var \Magefox\GoogleShopping\Helper\Products
     */
    private $_storeManager;

    public function __construct(
        \Fandi\GoogleShopping\Helper\Data $helper,
        \Fandi\GoogleShopping\Helper\Products $productFeedHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helper = $helper;
        $this->_productFeedHelper = $productFeedHelper;
        $this->_storeManager = $storeManager;
    }

    public function getProducts()
    {
        $productCollection = $this->_productFeedHelper->getFilteredProducts();

        $rows = [];
        foreach ($productCollection as $product) {
            $rows[] = $this->buildProduct($product);
        }
        return $rows;
    }

    public function buildProduct($product)
    {
        $_description = $this->fixDescription($product->getDescription());
        // $_condition = $product->getAttributeText('condition');

        $_finalPrice = number_format($product->getPrice(), 2, '.', '') . ' ' . $this->_productFeedHelper->getCurrentCurrencySymbol();

        return [
            $product->getId(), // "id"
            $product->getName(), // "judul"
            $_description, // "deskripsi"
            $product->getProductUrl(), // "link"
            'new', // is_array($_condition) ? $_condition[0] : $_condition, // "kondisi"
            $_finalPrice, // "harga"
            'in stock', // "ketersediaan"
            $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, true) . 'catalog/product' . $product->getImage(), // "link_gambar"
            $product->getSku(), // $product->getAttributeText('gr_ean'), // "gtin"
            $product->getSku(), // "mpn"
            $product->getAttributeText('manufacturer'), // "merek"
            $this->_productFeedHelper->getProductValue($product, 'google_product_category'), // 'kategori_produk_google'
        ];
    }

    public function fixDescription($data)
    {
        $description = $data;
        $encode = mb_detect_encoding($data);
        $description = mb_convert_encoding($description, 'UTF-8', $encode);

        $newlines = ["\r\n", "\n", "\r"];
        $description = str_replace($newlines, ' ', strip_tags(stripslashes($description)));

        return $description;
    }
}
