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

class Import
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    public $fileCsv;

    public $moduleReader;

    private $collection;

    private $column = [];

    private $data = [];

    private $size = 0;

    private $ignored = 0;

    public $giftCardFactory;

    public $giftCardCollectionFactory;

    public $giftCardSetCollectionFactory;

    public $templateCollectionFactory;

    public function __construct(
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $fileCsv,
        \Magento\Framework\Data\Collection $collection,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $collectionFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSet\CollectionFactory $giftCardSetCollectionFactory,
        \Magetrend\GiftCard\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    ) {
        $this->moduleReader = $moduleReader;
        $this->fileCsv = $fileCsv;
        $this->helper = $helper;
        $this->collection = $collection;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardCollectionFactory = $collectionFactory;
        $this->giftCardSetCollectionFactory = $giftCardSetCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    public function getCollectionFromFile($filePath)
    {
        $this->parseFile($filePath);
        $this->prepareCollection();
        $this->validateCollection();
        return $this->collection;
    }

    public function parseFile($filePath)
    {
        if (file_exists($filePath)) {
            $data = $this->fileCsv->getData($filePath);
            $this->column = array_shift($data);
            $this->data = $data;
            $this->size = count($data);
        }
    }

    public function prepareCollection()
    {
        if ($this->size == 0) {
            return false;
        }

        foreach ($this->data as $item) {
            $dataObject = new \Magento\Framework\DataObject();
            foreach ($item as $key => $value) {
                if (!isset($this->column[$key])) {
                    continue;
                }
                $columnName = $this->column[$key];

                $dataObject->setData($columnName, $value);
            }

            $this->collection->addItem($dataObject);
        }
        return true;
    }

    public function getColumnList($filePath)
    {
        $this->parseFile($filePath);
        return $this->column;
    }

    public function importData($postData, $filePath)
    {
        $this->getCollectionFromFile($filePath);

        if ($this->size == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Zero items in file'));
        }

        foreach ($this->column as $column) {
            if (isset($postData[$column])) {
                unset($postData[$column]);
            }
        }
        $postData['store_ids'] = $this->helper->convertStoreIdsToString($postData['store_ids']);
        foreach ($this->collection as $item) {
            $itemData = array_merge($item->getData(), $postData);
            $this->giftCardFactory->create()
                ->setData($itemData)
                ->save();
        }
    }

    public function validateCollection()
    {
        if ($this->size == 0) {
            return true;
        }

        if (!in_array('code', $this->column)) {
            $this->collection->removeAllItems();
            throw new \Magento\Framework\Exception\LocalizedException(__('Code Column not found in data file'));
        }

        $this->removeBadGiftCardCodes();
        $this->removeBadGiftCardSetIds();
        $this->removeBadTemplateIds();
    }

    public function removeBadGiftCardCodes()
    {
        $list = [];
        foreach ($this->collection as $item) {
            $list[] = $item->getCode();
        }

        $giftCardCollection = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('code', ['in' => $list]);

        if ($giftCardCollection->getSize() == 0) {
            return true;
        }

        $list = [];
        foreach ($giftCardCollection as $item) {
            $list[] = $item->getCode();
        }

        foreach ($this->collection as $key => $item) {
            if (in_array($item->getCode(), $list)) {
                $this->collection->removeItemByKey($key);
                $this->ignored++;
            }
        }
        return true;
    }

    public function removeBadGiftCardSetIds()
    {
        if (!in_array('gift_card_set_id', $this->column)) {
            return false;
        }

        $list = [];
        foreach ($this->collection as $item) {
            $list[] = $item->getGiftCardSetId();
        }

        $giftCardSetCollection = $this->giftCardSetCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $list]);

        $list = [];
        if ($giftCardSetCollection->getSize()  > 0) {
            foreach ($giftCardSetCollection as $giftCardSet) {
                $list[] = $giftCardSet->getId();
            }
        }

        foreach ($this->collection as $key => $item) {
            if (!in_array($item->getGiftCardSetId(), $list)) {
                $item->setGiftCardSetId(0);
            }
        }

        return true;
    }

    public function removeBadTemplateIds()
    {
        if (!in_array('template_id', $this->column)) {
            return false;
        }

        $list = [];
        foreach ($this->collection as $item) {
            $list[] = $item->getTemplateId();
        }

        $collection = $this->templateCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $list]);

        $list = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $template) {
                $list[] = $template->getId();
            }
        }

        foreach ($this->collection as $key => $item) {
            if (!in_array($item->getTemplateId(), $list)) {
                $item->setTemplateId(0);
            }
        }

        return true;
    }

    public function getIgnoredItemCount()
    {
        return $this->ignored;
    }

    public function getItemCount()
    {
        return $this->size;
    }

    public function getSize()
    {
        return $this->getItemCount() - $this->getIgnoredItemCount();
    }
}
