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

//@codingStandardsIgnoreFile
namespace Magetrend\GiftCard\Model\Template\Design;

class Two extends \Magetrend\GiftCard\Model\Template\Design
{
    public $imgWidth = 2625;

    public $imgHeight = 1125;

    public $qrW = 0;

    public $qrH = 0;

    public $titleW = 0;

    public $titleH = 0;

    public $title2H = 68;

    public $bgH = 685;

    public $codeLabelW = 645;

    public $contentStartY = 728;

    public $contentStartX = 156;

    public $defaultTitleSize = 134;

    public $contentLine1Size = 136;

    public function draw()
    {
        parent::draw();
        $this->drawBackground($this->getTemplate()->getImagePath('image_1'));
        $this->drawObjects();
        $this->drawPriceLabel();
        $this->drawQR();
        $this->drawTitle();
        $this->drawNote();
        $this->drawPrice();
        $this->drawCode();
        $this->drawLogo();
    }

    public function init()
    {
        parent::init();
        $giftCardTemplate = $this->getTemplate();
        $color1 = $this->helper->hex2rgb($giftCardTemplate->getColor1() != '' ? $giftCardTemplate->getColor1() : 'ad2075');
        $color2 = $this->helper->hex2rgb($giftCardTemplate->getColor2() != '' ? $giftCardTemplate->getColor2() : 'faa21d');
        $color3 = $this->helper->hex2rgb($giftCardTemplate->getColor3() != '' ? $giftCardTemplate->getColor3() : '49494a');
        $color4 = $this->helper->hex2rgb($giftCardTemplate->getColor4() != '' ? $giftCardTemplate->getColor4() : 'ffffff');

        $this->addColor('color1', $color1[0], $color1[1], $color1[2]);
        $this->addColor('color2', $color2[0], $color2[1], $color2[2]);
        $this->addColor('color3', $color3[0], $color3[1], $color3[2]);
        $this->addColor('color4', $color4[0], $color4[1], $color4[2]);


        $this->addFont('font1', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Light.ttf'));
        $this->addFont('font2', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Medium.ttf'));
        $this->addFont('font3', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Black.ttf'));
        $this->addFont('font4', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/myriad-web-pro.ttf'));
    }


    /**
     * Draw squares and circles on gift card
     */
    public function drawObjects()
    {
        $contentPanel = imagecreatetruecolor($this->imgWidth, 440);
        $color = $this->helper->hex2rgb(
            $this->getTemplate()->getColor2() != '' ? $this->getTemplate()->getColor2() : 'e3b492'
        );

        $leftSideColour = imagecolorallocatealpha($contentPanel, $color[0], $color[1], $color[2], 10);
        imagefill($contentPanel, 0, 0, $leftSideColour);
        imagecopy($this->getImg(), $contentPanel, 0, 685, 0, 0, $this->imgWidth, 440);

        imagefilledrectangle($this->getImg(), 0, $this->bgH + 18, $this->imgWidth, $this->bgH + 22, $this->getColor('color4'));
    }

    /**
     * Draw price label on gift card
     */
    public function drawPriceLabel()
    {
        $priceLabel = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/two/price2.png');
        imagefilledellipse($this->getImg(), 387, 384, 460, 460, $this->getColor('color1'));
        imagecopy($this->getImg(), imagecreatefrompng($priceLabel), 156, 153, 0, 0, $this->getImgWidth($priceLabel), $this->getImgHeight($priceLabel));
    }


    /**
     * Draw gift card title
     */
    public function drawTitle()
    {
        $template = $this->getTemplate();
        $title = $template->getText1() ? $template->getText1() : ' ';
        $fontSize = $this->defaultTitleSize;
        $font = $this->getFont('font3');

        $this->titleW = $this->getTextWidth($font, $fontSize, $title) + 78;
        $this->titleH = $this->getTextHeight($font, $fontSize, $title);
        imagettftext($this->getImg(), $fontSize, 0, $this->contentStartX + $this->qrW, ($this->contentStartY + (int)$fontSize), $this->getColor('color1'), $font, $title);
    }

    /**
     * Draw QR code
     */
    public function drawQR()
    {
        $qrData = $this->getTemplate()->getText6();
        $qrData = $this->filterQRData($qrData);
        if (empty($qrData) || !$qr = $this->getQR($qrData)) {
            return;
        }

        $this->qrW = imagesx($qr);
        $this->qrH = imagesy($qr);
        imagecolortransparent($qr, imagecolorexact($qr, 255, 255, 255));
        imagecopy($this->getImg(), $qr, $this->contentStartX-45, $this->contentStartY-45, 0, 0, $this->qrW, $this->qrH);
        //space after qr
        $this->qrW += 26;
    }

    /**
     * Draw logo on gift card
     */
    public function drawLogo()
    {
        $template = $this->getTemplate();
        $title2 = $template->getText2();
        if (empty($title2)) {
            return;
        }

        $fontSize = $this->title2H;
        $title2FirstWord = explode(' ', $title2);
        if (isset($title2FirstWord[0]))
            $title2FirstWord = $title2FirstWord[0];
        $font = $this->getFont('font4');

        $startX = $this->contentStartX + $this->qrW + $this->titleW + $this->codeLabelW;
        $startY = $this->contentStartY + $fontSize + (($this->contentLine1Size - $fontSize) / 2);

        imagettftext($this->getImg(), $fontSize, 0, $startX, $startY, $this->getColor('color3'), $font, $title2);
        imagettftext($this->getImg(), $fontSize, 0, $startX, $startY, $this->getColor('color1'), $font, $title2FirstWord);
    }

    /**
     * Write additional information on gift card
     */
    public function drawNote()
    {
        $template = $this->getTemplate();
        $giftCard = $this->getGiftCard();
        $validTo = $giftCard->getFormattedExpireDate();;

        $note = $template->getText3();
        $startX = $this->contentStartX + $this->qrW;
        $startY = $this->contentStartY + $this->contentLine1Size + 31;
        $height = 62;
        //v-align with QR bottom
        if ($this->qrH != 0) {
            if ($this->qrH <= $this->contentLine1Size + 31 + $height + 50) {
                $startY = $this->contentStartY + $this->qrH - $height;
            }
        }

        if (!empty($validTo)) {
            $expiredAtText = $template->getText5() ? $template->getText5() : __('Valid:');
            $expiredAtText = $expiredAtText . ' ' . $validTo;
            $note = $expiredAtText . '  ' . $note;
        }

        imagefilledrectangle($this->getImg(), $startX, $startY, $width = $this->imgWidth, $startY + $height, $this->getColor('color1'));
        imagettftext($this->getImg(), 30, 0, $startX + 40, $startY + 47, $this->getColor('color4'), $this->getFont('font2'), str_replace("|", "", $note));
    }

    /**
     * Draw price on price label
     */
    public function drawPrice()
    {
        $giftCard = $this->getGiftCard();
        $value = $giftCard->getFormattedValue();
        $font = $this->getFont('font3');
        $fontSize = 130;
        $step = 3;
        $maxWidth = 380;

        for ($i = 0; $i < 30; $i++) {
            $nextW = $this->getTextWidth($font, $fontSize + $step, $value);
            $prevW = $this->getTextWidth($font, $fontSize - $step, $value);

            if ($nextW < $maxWidth)
                $fontSize += $step;
            elseif ($prevW > $maxWidth)
                $fontSize -= $step;
            else
                break;
        }

        $currentW = $this->getTextWidth($font, $fontSize, $value);
        if ($maxWidth < $currentW) {
            $fontSize -= $step;
            $currentW = $this->getTextWidth($font, $fontSize, $value);
        }

        $currentH = $this->getTextHeight($font, $fontSize, '0');
        $x = 156 + ((473 - $currentW) / 2);
        $y = 153 + ((473 / 2) + ($currentH / 2));

        imagettftext($this->getImg(), $fontSize, 0, $x, $y, $this->getColor('color4'), $font, $value);
    }

    /**
     * Draw gift card code
     */
    public function drawCode()
    {
        $template = $this->getTemplate();
        $giftCard = $this->getGiftCard();
        $code = $giftCard->getCode();
        $font = $this->getFont('font3');
        $fontSize = 40;
        $maxWidth = 500;

        if ($this->getTextWidth($font, $fontSize, $code) > $maxWidth) {
            $step = 3;
            for ($i = 0; $i < 30; $i++) {
                $nextW = $this->getTextWidth($font, $fontSize + $step, $code);
                $prevW = $this->getTextWidth($font, $fontSize - $step, $code);

                if ($nextW < $maxWidth)
                    $fontSize += $step;
                elseif ($prevW > $maxWidth)
                    $fontSize -= $step;
                else
                    break;
            }

            $currentW = $this->getTextWidth($font, $fontSize, $code);
            if ($maxWidth < $currentW) {
                $fontSize -= $step;
            }
        }

        //gift card code rectangle
        $startX = $this->contentStartX + $this->qrW + $this->titleW;
        $width = $startX + $this->codeLabelW;
        $this->codeLabelW += 69;
        $height = $this->contentLine1Size;
        imagefilledrectangle($this->getImg(), $startX, $this->contentStartY, $width, $this->contentStartY + $height, $this->getColor('color1'));
        imagettftext($this->getImg(), 28, 0, $startX + 30, $this->contentStartY + 54, $this->getColor('color4'), $this->getFont('font2'), $template->getText4() ? $template->getText4() : __('Gift Card Code:'));;
        imagettftext($this->getImg(), $fontSize, 0, $startX + 30, $this->contentStartY + 110, $this->getColor('color4'), $font, $code);
    }
}
