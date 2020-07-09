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

class White extends \Magetrend\GiftCard\Model\Template\Design
{
    public $imgWidth = 2475;

    public $imgHeight = 3508;

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
        $this->fillBg();
        $this->drawHeader();
        $this->drawPriceLabel();
        $this->drawTitle();
        $this->drawPrice();
        $this->drawDotLines();
        $this->drawFooter();
        $this->drawCode();
        $this->drawDate();
        $this->drawNote();
    }

    public function init()
    {
        parent::init();
        $giftCardTemplate = $this->getTemplate();
        $color1 = $this->helper->hex2rgb($giftCardTemplate->getColor1() != '' ? $giftCardTemplate->getColor1() : 'ad2075');
        $color2 = $this->helper->hex2rgb($giftCardTemplate->getColor2() != '' ? $giftCardTemplate->getColor2() : 'faa21d');
        $color3 = $this->helper->hex2rgb($giftCardTemplate->getColor3() != '' ? $giftCardTemplate->getColor3() : '49494a');
        $color4 = $this->helper->hex2rgb($giftCardTemplate->getColor4() != '' ? $giftCardTemplate->getColor4() : 'ffffff');
        $color5 = $this->helper->hex2rgb($giftCardTemplate->getColor5() != '' ? $giftCardTemplate->getColor5() : 'ffffff');

        $this->addColor('color1', $color1[0], $color1[1], $color1[2]);
        $this->addColor('color2', $color2[0], $color2[1], $color2[2]);
        $this->addColor('color3', $color3[0], $color3[1], $color3[2]);
        $this->addColor('color4', $color4[0], $color4[1], $color4[2]);
        $this->addColor('color5', $color5[0], $color5[1], $color5[2]);


        $this->addFont('font1', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/gilroy-extrabold-webfont.ttf'));
        $this->addFont('font2', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/gilroy-light-webfont.ttf'));
    }

    /**
     * Draw price label on gift card
     */
    public function drawPriceLabel()
    {
        imagefilledellipse($this->getImg(), 1237, 1673, 624, 624, $this->getColor('color3'));
    }

    /**
     * Draw gift card title
     */
    public function drawTitle()
    {
        $template = $this->getTemplate();
        $title = $template->getText1() ? $template->getText1() : ' ';
        $fontSize = $this->defaultTitleSize;
        $font = $this->getFont('font1');

        $this->titleW = $this->getTextWidth($font, $fontSize, $title);
        $this->titleH = $this->getTextHeight($font, $fontSize, $title);
        imagettftext($this->getImg(), $fontSize, 0, ($this->imgWidth - $this->titleW) / 2, (1090 + (int)$fontSize), $this->getColor('color2'), $font, $title);
    }

    public function fillBg()
    {
        imagefilledrectangle($this->getImg(), 0, 0, $this->imgHeight, $this->imgHeight, $this->getColor('color1'));
    }

    /**
     * Draw logo on gift card
     */
    public function drawHeader()
    {
        $priceLabel = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/header.png');
        $imgWidth = $this->getImgWidth($priceLabel);
        $imgHeight = $this->getImgHeight($priceLabel);
        imagefilledrectangle($this->getImg(), 951, 0, 951+$imgWidth-1, $imgHeight-1, $this->getColor('color2'));
        imagecopy($this->getImg(), imagecreatefrompng($priceLabel), 951, 0, 0, 0, $imgWidth, $imgHeight);
    }

    /**
     * Draw dots
     */
    public function drawDotLines()
    {
        $giftCard = $this->getGiftCard();
        $validTo = $giftCard->getFormattedExpireDate();
        $startAt = 2140;
        $amount = empty($validTo)?2:3;
        $line = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/line.png');
        $imgWidth = $this->getImgWidth($line);
        $imgHeight = $this->getImgHeight($line);
        $x = ($this->imgWidth - $imgWidth) / 2;
        for ($i = 0; $i < $amount; $i++) {
            imagefilledrectangle($this->getImg(), $x, $startAt, $x+$imgWidth-1, $startAt+$imgHeight-1, $this->getColor('color2'));
            imagecopy($this->getImg(), imagecreatefrompng($line), $x, $startAt, 0, 0, $imgWidth, $imgHeight);
            $startAt += 185;
        }
    }

    /**
     * Draw logo on gift card
     */
    public function drawFooter()
    {
        $footer = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/footer.png');
        $imgWidth = $this->getImgWidth($footer);
        $imgHeight = $this->getImgHeight($footer);
        $x = ($this->imgWidth - $imgWidth) / 2;
        imagefilledrectangle($this->getImg(), $x, $this->imgHeight - $imgHeight, $x+$imgWidth-1, $this->imgHeight, $this->getColor('color2'));
        imagecopy($this->getImg(), imagecreatefrompng($footer), $x, $this->imgHeight - $imgHeight, 0, 0, $imgWidth, $imgHeight);
    }

    /**
     * Write additional information on gift card
     */
    public function drawDate()
    {
        $template = $this->getTemplate();
        $giftCard = $this->getGiftCard();
        $validTo = $giftCard->getFormattedExpireDate();

        if (empty($validTo)) {
            return;
        }

        $line = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/line.png');
        $imgWidth = $this->getImgWidth($line);
        $font = $this->getFont('font1');
        $fontSize = 42;

        $expiredAtText = $template->getText5() ? $template->getText5() : __('Valid:');
        $textX = ($this->imgWidth - $imgWidth) / 2 + 30;
        imagettftext($this->getImg(), 42, 0, $textX, 2256 + 185, $this->getColor('color2'), $this->getFont('font1'), $expiredAtText);


        $dateWidth = $this->getTextWidth($font, $fontSize, $validTo);
        $dateX = ($this->imgWidth - $imgWidth) / 2 + $imgWidth - 30 - $dateWidth;
        imagettftext($this->getImg(), $fontSize, 0, $dateX, 2256 + 185, $this->getColor('color5'), $font, $validTo);

    }

    /**
     * Write additional information on gift card
     */
    public function drawNote()
    {
        $template = $this->getTemplate();
        $giftCard = $this->getGiftCard();
        $validTo = $giftCard->getFormattedExpireDate();
        $note = $template->getText3();

        if (empty($note)) {
            return;
        }

        $line = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/line.png');
        $imgWidth = $this->getImgWidth($line);
        $font = $this->getFont('font2');
        $fontSize = 36;

        $noteWidth = $this->getTextWidth($font, $fontSize, $note);
        $noteX = ($this->imgWidth - $imgWidth) / 2 + $imgWidth - 30 - $noteWidth;
        imagettftext($this->getImg(), $fontSize, 0, $noteX, 2256 + 185 * 2, $this->getColor('color2'), $font, $note);
   }

    /**
     * Draw price on price label
     */
    public function drawPrice()
    {
        $giftCard = $this->getGiftCard();
        $value = $giftCard->getFormattedValue();
        $font = $this->getFont('font1');
        $fontSize = 42;
        $step = 3;
        $maxWidth = 624;

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

        //1237, 1673, 624, 624,


        $currentH = $this->getTextHeight($font, $fontSize, '0');
        $x = 925 + ((624 - $currentW) / 2);
        $y = 1361 + ((624 / 2) + ($currentH / 2));

        imagettftext($this->getImg(), $fontSize, 5, $x, $y, $this->getColor('color4'), $font, $value);
    }

    /**
     * Draw gift card code
     */
    public function drawCode()
    {
        $template = $this->getTemplate();
        $giftCard = $this->getGiftCard();
        $code = $giftCard->getCode();

        $line = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/white/line.png');
        $imgWidth = $this->getImgWidth($line);
        $font = $this->getFont('font1');
        $fontSize = 40;
        $maxWidth = 500;

        $textX = ($this->imgWidth - $imgWidth) / 2 + 30;
        imagettftext($this->getImg(), 42, 0, $textX, 2256, $this->getColor('color2'), $this->getFont('font1'), $template->getText4() ? $template->getText4() : __('Gift Card Code:'));;


        $codeWidth = $this->getTextWidth($font, $fontSize, $code);
        $codeX = ($this->imgWidth - $imgWidth) / 2 + $imgWidth - 30 - $codeWidth;
        imagettftext($this->getImg(), $fontSize, 0, $codeX, 2256, $this->getColor('color2'), $font, $code);
    }
}
