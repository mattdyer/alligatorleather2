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

namespace Magetrend\GiftCard\Model\Template;

class Design
{
    /**
     * @var resource|null
     */
    private $imgResource = null;

    /**
     * @var int
     */
    public $imgWidth = 2625;

    /**
     * @var int
     */
    public $imgHeight = 1125;

    /**
     * @var array
     */
    public $colors = [];

    /**
     * @var array
     */
    public $fonts = [];

    /**
     * @var \Magetrend\GiftCard\Model\Template|null
     */
    private $template;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCard|null
     */
    private $giftCard;

    /**
     * @var Media\Config
     */
    public $mediaConfig;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepo;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    public $directoryList;

    /**
     * @var \Magetrend\GiftCard\Helper\QRcode
     */
    public $qr;

    /**
     * Design constructor.
     *
     * @param Media\Config $config
     * @param \Magento\Framework\View\Asset\Repository $repository
     * @param \Magetrend\GiftCard\Helper\Data $data
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magetrend\GiftCard\Helper\QRcode $QRcode
     */
    public function __construct(
        \Magetrend\GiftCard\Model\Template\Media\Config $config,
        \Magento\Framework\View\Asset\Repository $repository,
        \Magetrend\GiftCard\Helper\Data $data,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magetrend\GiftCard\Helper\QRcode $QRcode
    ) {
        $this->mediaConfig = $config;
        $this->assetRepo = $repository;
        $this->helper = $data;
        $this->directoryList = $directoryList;
        $this->qr = $QRcode;
    }

    /**
     * Draw gift card
     */
    public function draw()
    {
        $this->init();
    }

    /**
     * Preapre gift card
     */
    public function init()
    {
        $this->imgResource = imagecreatetruecolor($this->imgWidth, $this->imgHeight);
        $this->addColor('black', 0, 0, 0);
        $this->addColor('white', 255, 255, 255);
    }

    /**
     * Draw gift card background
     * @param string $backgroundImage
     */
    public function drawBackground($backgroundImage = '')
    {
        if (!empty($backgroundImage)) {
            $imgWidth = $this->getImgWidth($backgroundImage, true);
            $imgHeight = $this->getImgHeight($backgroundImage, true);
            $imageType = explode('.', $backgroundImage);
            $imageType = strtolower(end($imageType));

            if ($imageType == 'png') {
                $image = imagecreatefrompng($backgroundImage);
            } elseif (in_array($imageType, ['jpg', 'jpeg'])) {
                $image = imagecreatefromjpeg($backgroundImage);
            } elseif ($imageType == 'gif') {
                $image = imagecreatefromgif($backgroundImage);
            } else {
                return;
            }

            imagecopy($this->getImg(), $image, 0, 0, 0, 0, $imgWidth, $imgHeight);
        }
    }

    /**
     * Returns image resource
     *
     * @return resource
     */
    public function getImg()
    {
        return $this->imgResource;
    }

    /**
     * Returns image width
     *
     * @param $imagePath
     * @return int
     */
    public function getImgWidth($imagePath)
    {
        $img = getimagesize($imagePath);
        return $img[0];
    }

    /**
     * Returns image height
     *
     * @param $imagePath
     * @return int
     */
    public function getImgHeight($imagePath)
    {
        $img = getimagesize($imagePath);
        return $img[1];
    }

    /**
     * Set gift card template object
     *
     * @param \Magetrend\GiftCard\Model\Template $template
     */
    public function setTemplate(\Magetrend\GiftCard\Model\Template $template)
    {
        $this->template = $template;
    }

    /**
     * Set gift card object
     *
     * @param \Magetrend\GiftCard\Model\GiftCard $giftCard
     */
    public function setGiftCard(\Magetrend\GiftCard\Model\GiftCard $giftCard)
    {
        $this->giftCard = $giftCard;
    }

    /**
     * Returns gift card template object
     *
     * @return \Magetrend\GiftCard\Model\Template|null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns gift card object
     *
     * @return \Magetrend\GiftCard\Model\GiftCard|null
     */
    public function getGiftCard()
    {
        return $this->giftCard;
    }

    /**
     * Returns image source
     *
     * @return mixed|string
     */
    public function getImageSource()
    {
        ob_start();
        imagejpeg($this->getImg(), null, 100);
        $image = ob_get_contents();
        ob_end_clean();
        $image = substr_replace($image, pack("Cnn", 0x01, 300, 300), 13, 5);
        return $image;
    }

    /**
     * Resize current image
     *
     * @param $newWidth
     */
    public function resize($newWidth)
    {
        $newHeight = $this->imgHeight * ($newWidth / $this->imgWidth);
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled(
            $thumb,
            $this->getImg(),
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $this->imgWidth,
            $this->imgHeight
        );
        $this->imgResource = $thumb;
    }

    /**
     * Returns text width
     *
     * @param $font
     * @param $size
     * @param $text
     * @return number
     */
    public function getTextWidth($font, $size, $text)
    {
        $arSize = imagettfbbox($size, 0, $font, $text);
        return abs($arSize[2] - $arSize[0]);
    }

    /**
     * Returns text height
     *
     * @param $font
     * @param $size
     * @param $text
     * @return number
     */
    public function getTextHeight($font, $size, $text)
    {
        $arSize = imagettfbbox($size, 0, $font, $text);
        return abs($arSize[7] - $arSize[1]);
    }

    /**
     * Returns QR code image
     *
     * @return bool|resource
     */
    public function getQR($qrData)
    {
        $qrImage = $this->directoryList->getPath('var').'/tmp/qr_'.md5($qrData).'.png';
        $this->helper->createDirIfNotExist($qrImage);
        $this->qr->png($qrData, $qrImage);
        $qr = imagecreatefrompng($qrImage);
        unlink($qrImage);
        return $qr;
    }

    /**
     * Add color
     *
     * @param $key
     * @param int $r
     * @param int $g
     * @param int $b
     */
    public function addColor($key, $r = 255, $g = 255, $b = 255)
    {
        $this->colors[$key] = imagecolorallocate($this->getImg(), $r, $g, $b);
    }

    /**
     * Add font face
     *
     * @param $key
     * @param $fontPath
     */
    public function addFont($key, $fontPath)
    {
        $this->fonts[$key] = $fontPath;
    }

    /**
     * Returns color
     *
     * @param $key
     * @return mixed
     */
    public function getColor($key)
    {
        return $this->colors[$key];
    }

    /**
     * Returns font face
     *
     * @param $key
     * @return mixed
     */
    public function getFont($key)
    {
        return $this->fonts[$key];
    }

    /**
     * Save resource to file
     *
     * @param $path
     */
    public function save($path)
    {
        imagejpeg($this->getImg(), $path, 100);
    }

    public function filterQRData($qrData)
    {
        $giftCard = $this->getGiftCard();
        $giftCardData = $giftCard->getData();

        if (empty($giftCardData)) {
            return $qrData;
        }

        foreach ($giftCardData as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $qrData = str_replace('{'.$key.'}', $value, $qrData);
            }
        }

        return $qrData;
    }
}
