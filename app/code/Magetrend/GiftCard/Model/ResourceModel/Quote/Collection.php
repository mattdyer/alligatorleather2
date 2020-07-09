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

namespace Magetrend\GiftCard\Model\ResourceModel\Quote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\Quote', 'Magetrend\GiftCard\Model\ResourceModel\Quote');
    }

    public function joinGiftCard($columns = ['*'])
    {
        $columns['gift_card_id'] = 'entity_id';
        $this->getSelect()
           ->join(
               [
                   'gc' => $this->getTable('mt_giftcard')
               ],
               'gc.entity_id = main_table.gift_card_id',
               $columns
           );

        return $this;
    }
}
