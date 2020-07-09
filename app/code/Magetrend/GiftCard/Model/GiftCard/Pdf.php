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

namespace Magetrend\GiftCard\Model\GiftCard;

class Pdf
{
    private $pdf;

    public $helper;

    public function __construct(
        \Magetrend\GiftCard\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function jpgToPdf($path, $imagePath)
    {
        $this->pdf = new \Zend_Pdf();
        $this->pdf->pages = [];
        $this->helper->createDirIfNotExist($path);
        $page = new \Zend_Pdf_Page('a4');
        $image = new \Zend_Pdf_Resource_Image_Jpeg($imagePath);

        $imgWidthPts = $image->getPixelWidth() * 72 / 96;
        $imgHeightPts = $image->getPixelHeight() * 72 / 96;

        $rate = $imgWidthPts / $page->getWidth();
        $imgWidthPts = $imgWidthPts / $rate;
        $imgHeightPts = $imgHeightPts / $rate;
        $pageHeight = $page->getHeight();

        $page->drawImage($image, 0, $pageHeight - $imgHeightPts, $imgWidthPts, $pageHeight);
        $this->pdf->pages[] = $page;
        $this->pdf->save($path);
        return $path;
    }
}
