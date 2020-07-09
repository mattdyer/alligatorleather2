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

namespace Magetrend\GiftCard\Model\Checkout\Cart;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class RequestInfoFilterComposite
 */
class GiftCardManagement implements \Magetrend\GiftCard\Api\GiftCardManagementInterface
{

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    public $quoteRepository;

    /**
     * @var \Magetrend\GiftCard\Model\Quote
     */
    public $giftCardQuote;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     * @param \Magetrend\GiftCard\Model\Quote  $giftCardQuote Gift Card Quote.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->giftCardQuote = $giftCardQuote;
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function add($cartId, $giftCardCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        if (!$this->giftCardQuote->validateGiftCardCode($giftCardCode)) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }
        try {
            $this->giftCardQuote->addGiftCardToQuote($giftCardCode, $quote->getId());
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift card code'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $giftCardCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $giftCard = $this->giftCardFactory->create()->load($giftCardCode, 'code');
        if (!$quote->getItemsCount() || !$giftCard->getId()) {
            throw new NoSuchEntityException(__('Could not remove gift card code'));
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $this->giftCardQuote->removeGiftCardFromQuote($giftCard->getId(), $quote->getId());
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove gift card code'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        $response = [];
        $giftCardCollection = $this->giftCardQuote->getGiftCardCollection($cartId);
        if ($giftCardCollection->getSize() > 0) {
            foreach ($giftCardCollection as $giftCard) {
                $response[] = [
                    'id' => $giftCard->getId(),
                    'code' => $giftCard->getCode(),
                    'balance' => $giftCard->getFormattedBalance()
                ];
            }
        }

        return json_encode(
            ['listData' => $response, 'isEnabled' => $this->giftCardQuote->isGiftCardProductItemInCart()?0:1]
        );
    }
}
