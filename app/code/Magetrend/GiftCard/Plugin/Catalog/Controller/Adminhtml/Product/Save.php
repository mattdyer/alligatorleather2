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

namespace  Magetrend\GiftCard\Plugin\Catalog\Controller\Adminhtml\Product;

class Save
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSetProductFactory
     */
    public $giftCardSetProductFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory
     */
    public $collectionFactory;

    public $giftCardSetFactory;

    public $js;

    public $helper;

    /**
     * Save constructor.
     * @param \Magetrend\GiftCard\Model\GiftCardSetProductFactory $giftCardSetProductFactory
     * @param \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Backend\Helper\Js $js
     * @param \Magetrend\GiftCard\Helper\Data $helper
     */
    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardSetProductFactory $giftCardSetProductFactory,
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Backend\Helper\Js $js,
        \Magetrend\GiftCard\Helper\Data $helper
    ) {
        $this->giftCardSetProductFactory = $giftCardSetProductFactory;
        $this->request = $requestInterface;
        $this->collectionFactory = $collectionFactory;
        $this->giftCardSetFactory = $giftCardSetFactory;
        $this->js = $js;
        $this->helper = $helper;
    }

    /**
     * Execute plugin
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Save $controller
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $controller, $result)
    {
        if ($this->helper->isM20()) {
            $this->updateProductM20();
        } else {
            $this->updateProduct();
        }

        return $result;
    }

    private function updateProductM20()
    {
        $productId = $this->request->getParam('id');
        $giftCardData = $this->request->getPost('giftcard', false);

        if (!$giftCardData || !isset($giftCardData['set']) || !is_numeric($productId)) {
            return;
        }

        $giftCardSetData = $this->js->decodeGridSerializedInput($giftCardData['set']);

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', $productId);

        $assignedSetsArray = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                $assignedSetsArray[$item->getGiftCardSetId()] = $item;
            }
        }

        foreach ($giftCardSetData as $giftCardSetId => $link) {
            $link['id'] = $giftCardSetId;
            if (isset($assignedSetsArray[$giftCardSetId])) {
                $this->updateRelation($link, $assignedSetsArray[$giftCardSetId]);
                unset($assignedSetsArray[$giftCardSetId]);
            } else {
                $this->createRelation($link, $productId);
            }
        }

        $this->deleteUnassignedSets($assignedSetsArray);
    }

    private function updateProduct()
    {
        $productId = $this->request->getParam('id');
        $linksData = $this->request->getPost('links', false);

        if (!$linksData
            || !isset($linksData['assigngiftcardset'])
            || count($linksData['assigngiftcardset']) == 0
            || !is_numeric($productId)
        ) {
            return;
        }

        $giftCardSetData = $linksData['assigngiftcardset'];
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', $productId);

        $assignedSetsArray = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                $assignedSetsArray[$item->getGiftCardSetId()] = $item;
            }
        }

        foreach ($giftCardSetData as $link) {
            if (!isset($link['id'])) {
                continue;
            }

            $giftCardSetId = $link['id'];
            if (isset($assignedSetsArray[$giftCardSetId])) {
                $this->updateRelation($link, $assignedSetsArray[$giftCardSetId]);
                unset($assignedSetsArray[$giftCardSetId]);
            } else {
                $this->createRelation($link, $productId);
            }
        }

        $this->deleteUnassignedSets($assignedSetsArray);
    }

    /**
     * Update Gift Card Set - Product relation
     * @param array $link
     * @param \Magetrend\GiftCard\Model\GiftCardSetProduct $relationObject
     */
    private function updateRelation($link, $relationObject)
    {
        if (!isset($link['price']) || $link['price'] <= 0) {
            $price = $this->getGiftCardSetPrice($link['id']);
        } else {
            $price = $link['price'];
        }

        $relationObject->setPrice($price)
            ->setPosition($link['position'])
            ->save();
    }

    /**
     * Create new Gift Card Set - Product relation
     * @param array $linkData
     * @param integer $productId
     */
    private function createRelation($linkData, $productId)
    {
        if (!isset($linkData['price']) || $linkData['price'] <= 0) {
            $price = $this->getGiftCardSetPrice($linkData['id']);
        } else {
            $price = $linkData['price'];
        }
        $this->giftCardSetProductFactory->create()
            ->setData([
                'product_id' => $productId,
                'gift_card_set_id' => $linkData['id'],
                'position' => $linkData['position'],
                'price' => $price,
            ])
            ->save();
    }

    /**
     * Delete Gift Card Set - Product relation
     * @param array $unassignedSets
     */
    private function deleteUnassignedSets($unassignedSets)
    {
        if (count($unassignedSets) == 0) {
            return;
        }
        foreach ($unassignedSets as $item) {
            $item->delete();
        }
    }

    protected function getGiftCardSetPrice($id)
    {
        $giftCardSet = $this->giftCardSetFactory->create()->load($id);
        return$giftCardSet->getValue();
    }
}
