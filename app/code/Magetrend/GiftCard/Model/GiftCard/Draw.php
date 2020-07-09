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

class Draw
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    private $design = null;

    private $template = null;

    private $giftCard = null;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Draw gift card image
     *
     * @return void
     */
    public function draw()
    {
        $this->getDesign()->setTemplate($this->template);
        $this->getDesign()->setGiftCard($this->giftCard);
        $this->getDesign()->draw();
    }

    /**
     * Returns image resource link
     *
     * @return mixed
     */
    public function getImageSource()
    {
        return $this->getDesign()->getImageSource();
    }

    /**
     * Resize image
     *
     * @param $width
     * @return mixed
     */
    public function resize($width)
    {
        return $this->getDesign()->resize($width);
    }

    /**
     * Set gift card template object
     *
     * @param \Magetrend\GiftCard\Model\Template $template
     */
    public function setTemplate(\Magetrend\GiftCard\Model\Template $template)
    {
        $this->design = null;
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
     * Returns gift card design model
     *
     * @return mixed|null
     */
    public function getDesign()
    {
        if ($this->design == null) {
            $designCode = $this->template->getDesign();
            $this->design = $this->objectManager->create(
                str_replace('[DESIGN]', ucfirst($designCode), 'Magetrend\GiftCard\Model\Template\Design\[DESIGN]')
            );
        }
        return $this->design;
    }

    /**
     * Save gift card image
     *
     * @param $path
     */
    public function save($path)
    {
        $this->getDesign()->save($path);
    }
}
