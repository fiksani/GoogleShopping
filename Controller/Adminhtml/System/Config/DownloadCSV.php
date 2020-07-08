<?php

namespace Fandi\GoogleShopping\Controller\Adminhtml\System\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadCSV extends \Magento\Backend\App\Action
{
    protected $directory;
    protected $feed;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Fandi\GoogleShopping\Model\Feed $feed
    ) {
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->feed = $feed;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $filepath = 'googleshopping/products-googleshopping.csv';
        $this->directory->create('googleshopping');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        $header = ['id', 'judul', 'deskripsi', 'link', 'kondisi', 'harga', 'ketersediaan', 'link_gambar', 'gtin', 'mpn', 'merek', 'kategori_produk_google'];
        $stream->writeCsv($header);

        $products = $this->feed->getProducts();
        foreach ($products as $product) {
            $stream->writeCsv($product);
        }

        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '1';

        $csvfilename = 'products-googleshopping.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }
}
