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

class One extends \Magetrend\GiftCard\Model\Template\Design
{
    public $titleSize = 120;

    public $title2Size = 55;

    public $qrW = 0;

    public $qrH = 0;

    public $title2H = 0;

    public $bottomContentY = 774;

    public $contentX = 148;

    /**
     * Draw gift card
     */
    public function draw()
    {
        parent::draw();
        $this->drawBackground($this->getTemplate()->getImagePath('image_1'));
        $this->drawShadow();
        $this->drawObjects();
        $this->drawPriceLabel();
        $this->drawTitle();
        $this->drawQR();
        $this->drawLogo();
        $this->drawNote();
        $this->drawPrice();
        $this->drawCode();
    }

    /**
     * Prepare colors and fonts
     */
    public function init()
    {
        parent::init();
        $giftCardTemplate = $this->getTemplate();
        $color1 = $this->helper->hex2rgb($giftCardTemplate->getColor1()!=''?$giftCardTemplate->getColor1():'ad2075');
        $color2 = $this->helper->hex2rgb($giftCardTemplate->getColor2()!=''?$giftCardTemplate->getColor2():'faa21d');
        $color3 = $this->helper->hex2rgb($giftCardTemplate->getColor3()!=''?$giftCardTemplate->getColor3():'49494a');
        $color4 = $this->helper->hex2rgb($giftCardTemplate->getColor4()!=''?$giftCardTemplate->getColor4():'ffffff');
        $color5 = $this->helper->hex2rgb($giftCardTemplate->getColor5()!=''?$giftCardTemplate->getColor5():'ffffff');
        
        $this->addColor('color1', $color1[0], $color1[1], $color1[2]);
        $this->addColor('color2', $color2[0], $color2[1], $color2[2]);
        $this->addColor('color3', $color3[0], $color3[1], $color3[2]);
        $this->addColor('color4', $color4[0], $color4[1], $color4[2]);
        $this->addColor('color5', $color5[0], $color5[1], $color5[2]);

        $this->addFont('font1', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Light.ttf'));
        $this->addFont('font2', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Medium.ttf'));
        $this->addFont('font3', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/Roboto-Black.ttf'));
        $this->addFont('font4', $this->mediaConfig->getFullPath('Magetrend_GiftCard::fonts/design/myriad-web-pro.ttf'));
    }

    /**
     * Draw shadow on gift card
     */
    public function drawShadow()
    {
        $shadowImage = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/one/shadow.png');
        imagecopy(
            $this->getImg(),
            imagecreatefrompng($shadowImage),
            1002,
            0,
            0,
            0,
            $this->getImgWidth($shadowImage),
            $this->getImgHeight($shadowImage)
        );
    }

    /**
     * Draw abstract objects
     */
    public function drawObjects()
    {
        $leftSide = imagecreatetruecolor(1002, 1125);
        $color = $this->helper->hex2rgb(
            $this->getTemplate()->getColor2()!=''?$this->getTemplate()->getColor2():'faa21d'
        );
        $leftSideColour = imagecolorallocatealpha($leftSide, $color[0], $color[1], $color[2], 10);
        imagefill($leftSide, 0, 0, $leftSideColour);
        imagecopy($this->getImg(), $leftSide, 0, 0, 0, 0, 1002, 1125);

        //gift card code rectangle
        if ($this->getGiftCard()->getValidTo() != '') {
            imagefilledrectangle($this->getImg(), 148, 631, 800, 690, $this->getColor('white'));
            imagefilledrectangle($this->getImg(), 148, 490, 800, 630, $this->getColor('color1'));
        } else {
            imagefilledrectangle($this->getImg() , 148 , 515 , 800 ,  645 , $this->getColor('color1'));
        }
    }

    /**
     * Draw gift card title
     */
    public function drawTitle()
    {
        $template = $this->getTemplate();
        $title = $template->getText1()?$template->getText1():' ';
        $fontSize = $template->getSize1()?$template->getSize1():$template->getDesignDefault('size_1');
        $startY = $template->getSize3();

        if (substr_count($title, '|') > 0) {
            $title = explode('|', $title);
            foreach ($title as $key => $line) {
                imagettftext($this->getImg(), $fontSize, 0, 149, $startY+1, $this->getColor('white'), $this->getFont('font1'), $line);
                imagettftext($this->getImg(), $fontSize, 0, 148, $startY, $this->getColor('color3'), $this->getFont('font1'), $line);
                if ($key == 0) {
                    imagettftext($this->getImg(), $fontSize, 0, 148, $startY, $this->getColor('color1'), $this->getFont('font1'), $line);
                }

                $startY = $startY + (int)$fontSize * 1.2;
            }
        } else {
            imagettftext($this->getImg(), $fontSize, 0, 149, $startY+1, $this->getColor('white'), $this->getFont('font1'), $title);
            imagettftext($this->getImg(), $fontSize, 0, 148, $startY, $this->getColor('color3'), $this->getFont('font1'), $title);
            $wordArray = explode(' ', $title);
            if (count($wordArray) <= 2) {
                imagettftext($this->getImg(), $fontSize, 0, 148, $startY, $this->getColor('color1'), $this->getFont('font1'), $wordArray[0]);
            }
        }
    }

    /**
     * Draw logo on gift card
     */
    public function drawLogo()
    {
        $template = $this->getTemplate();
        $title2 = $template->getText2();
        if (empty($title2)) {
            return false;
        }
        $fontSize = $template->getSize2()?$template->getSize2():$this->title2Size;
        $startY = $this->bottomContentY+$fontSize;
        $startX = $this->contentX+$this->qrW;
        $font = $this->getFont('font4');

        if (substr_count($title2, '|') > 0) {
            $title = explode('|', $title2);
            foreach ($title as $key => $line) {
                if (count($title) == $key+1) {
                    imagettftext($this->getImg(), $fontSize, 0, $startX, $startY , $this->getColor('color1'), $font, $line);
                } else
                    imagettftext($this->getImg(), $fontSize, 0, $startX, $startY , $this->getColor('color3'), $font, $line);
                $startY=$startY+$fontSize+20;
                $this->title2H += $fontSize+20;
            }
            $this->title2H += 20;
        } else {
            $this->title2H = $fontSize+40;
            imagettftext($this->getImg(), $fontSize, 0, $startX, $startY , $this->getColor('color3'), $font, $title2);
            $lastWord = explode(' ',$title2);
            if (count($lastWord) > 1) {
                unset($lastWord[count($lastWord)-1]);
                $text = implode(' ', $lastWord);
                imagettftext($this->getImg(), $fontSize, 0, $startX, $startY , $this->getColor('color1'), $font, $text);
            }
        }

        return true;
    }

    /**
     * write additional information on gift card
     */
    public function drawNote()
    {
        $template = $this->getTemplate();
        $note = $template->getText3();
        $startY = $this->bottomContentY+$this->title2H+20;
        $startX = $this->contentX+$this->qrW;
        imagettftext($this->getImg(), 26, 0, $startX , $startY , $this->getColor('color5'), $this->getFont('font2'), str_replace("|","\n",$note));
    }

    /**
     * Draw QR code on gift card
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
        imagecopy($this->getImg(), $qr, $this->contentX-45, $this->bottomContentY-45, 0, 0, $this->qrW, $this->qrH);
        $this->qrW = $this->qrW - 45;
    }

    /**
     * Draw price label on gift card
     */
    public function drawPriceLabel()
    {
        $priceLabel = $this->mediaConfig->getFullPath('Magetrend_GiftCard::images/design/one/price.png');
        imagefilledellipse($this->getImg(), 1008, 575, 463, 463, $this->getColor('color1'));
        imagecopy($this->getImg(),imagecreatefrompng($priceLabel), 758, 330, 0, 0, $this->getImgWidth($priceLabel), $this->getImgHeight($priceLabel));
    }

    /**
     * Calculate price position and draw it
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
            $nextW = $this->getTextWidth($font,$fontSize+$step, $value);
            $prevW = $this->getTextWidth($font,$fontSize-$step, $value);

            if ($nextW < $maxWidth )
                $fontSize+=$step;
            elseif ($prevW > $maxWidth)
                $fontSize-=$step;
            else
                break;
        }

        $currentW = $this->getTextWidth($font, $fontSize, $value);
        if ($maxWidth < $currentW) {
            $fontSize-=$step;
            $currentW = $this->getTextWidth($font, $fontSize, $value);
        }

        $currentH = $this->getTextHeight($font,$fontSize, '0');
        $x = 758 + ((491 - $currentW)/2);
        $y = 330 + ((491/2)+($currentH/2));

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
        $expiredAt = $giftCard->getFormattedExpireDate();
        $font = $this->getFont('font3');
        $fontSize = 40;
        $maxWidth = 500;

        if ($this->getTextWidth($font,$fontSize, $code) > $maxWidth) {
            $step = 3;
            for ($i = 0; $i < 30; $i++) {
                $nextW = $this->getTextWidth($font,$fontSize+$step, $code);
                $prevW = $this->getTextWidth($font,$fontSize-$step, $code);

                if ($nextW < $maxWidth )
                    $fontSize+=$step;
                elseif ($prevW > $maxWidth)
                    $fontSize-=$step;
                else
                    break;
            }

            $currentW = $this->getTextWidth($font,$fontSize, $code);
            if ($maxWidth < $currentW) {
                $fontSize-=$step;
            }
        }

        if (!empty($expiredAt)) {
            $date = $expiredAt; //Mage::helper('core')->formatDate($expiredAt, 'medium', false);
            $expiredAtText = $template->getText5()?$template->getText5():__('Valid:');
            $expiredAtText = $expiredAtText . ' ' . $date;
            imagettftext($this->getImg(), 28, 0, 185 , 676 , $this->getColor('color1'), $this->getFont('font1'), $expiredAtText);
            imagettftext($this->getImg(), 28, 0, 185, 550, $this->getColor('color4'), $this->getFont('font4'), $template->getText4()?$template->getText4():__('Gift Card Code:'));
            imagettftext($this->getImg(), $fontSize, 0, 185, 600, $this->getColor('color4'), $font, $code);
        } else {
            //if has not expiredAt than y + 15
            imagettftext($this->getImg(), 28, 0, 185, 565, $this->getColor('color4'), $this->getFont('font4'), $template->getText4()?$template->getText4():__('Gift Card Code:'));
            imagettftext($this->getImg(), $fontSize, 0, 185, 615, $this->getColor('color4'), $font, $code);

        }
    }
}
