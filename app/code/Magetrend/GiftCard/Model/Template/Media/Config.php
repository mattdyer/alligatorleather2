<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * PHP version 5.3 or later
 *
 * @category MageTrend
 * @package  Magetend/GiftCard
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-gift-card
 */

namespace Magetrend\GiftCard\Model\Template\Media;

use Magento\Framework\App\Filesystem\DirectoryList;

class Config
{
    CONST TMP_DIR = 'giftcard';
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepo;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    public $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $file;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * Config constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\Repository $repository
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $repository,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->assetRepo = $repository;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->file = $file;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return 'mt/giftcard/background';
    }
    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return 'mt/giftcard/tmp';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'mt/giftcard/background';
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     * @return string
     */
    public function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Returns file path
     * @param $fileName
     * @return string
     */
    public function getFullPath($fileName)
    {
        if (substr_count($fileName, 'Magetrend_GiftCard') == 1) {
            $imagePath = $this->directoryList->getPath('static');
            $asset = $this->assetRepo->createAsset($fileName, ['area' => \Magento\Framework\App\Area::AREA_ADMINHTML]);
            $imagePath .= '/'.$asset->getPath();
            if (!$this->file->fileExists($imagePath)) {
                $imagePath = $this->getTmpFilePath($asset, $fileName);
            }
        } else {
            $imagePath = $this->directoryList->getPath('media').'/'.$this->getMediaPath($fileName);
        }

        return $imagePath;
    }

    /**
     * @param \Magento\Framework\View\Asset\File $fileAssest
     */
    public function getTmpFilePath($fileAssest, $fileName)
    {
        $fileExt = explode('.', $fileName);
        $fileExt = end($fileExt);
        $tmpPath = self::TMP_DIR.'/'.md5($fileName).'.'.$fileExt;

        /** @var \Magento\Framework\Filesystem\Directory\Write $directory */
        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $directory->create(self::TMP_DIR);
        $directory->writeFile($tmpPath, $fileAssest->getContent());
        return $directory->getAbsolutePath($tmpPath);
    }
}
