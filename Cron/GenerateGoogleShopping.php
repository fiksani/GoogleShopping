<?php

namespace Fandi\GoogleShopping\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;

class GenerateGoogleShopping
{
    protected $filesystem;
    protected $feed;
    protected $logger;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Fandi\GoogleShopping\Model\Feed $feed,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->feed = $feed;
        $this->logger = $logger;

        $this->directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    public function execute()
    {
        $this->logger->info('Running cron googleshopping');

        $filepath = 'googleshopping/products.csv';
        $this->directory->create('googleshopping');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        $header = ['id', 'judul', 'deskripsi', 'link', 'kondisi', 'harga', 'ketersediaan', 'link_gambar', 'gtin', 'mpn', 'merek', 'kategori_produk_google'];
        $stream->writeCsv($header);

        $products = $this->feed->getProducts();
        foreach ($products as $product) {
            $stream->writeCsv($product);
        }
    }
}
