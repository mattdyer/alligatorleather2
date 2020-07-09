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

namespace  Magetrend\GiftCard\Ui\Component\MassAction\GiftCard;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

class Status implements JsonSerializable
{
    /**
     * @var array
     */
    public $options;

    /**
     * Additional options params
     *
     * @var array
     */
    public $data;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    public $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    public $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    public $additionalData = [];

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\Status
     */
    public $configStatus;

    /**
     * Status constructor.
     * @param UrlInterface $urlBuilder
     * @param \Magetrend\GiftCard\Model\Config\Source\Status $status
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magetrend\GiftCard\Model\Config\Source\Status $status,
        array $data = []
    ) {
        $this->configStatus = $status;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = $this->configStatus->toOptionArray();

            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'status_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    public function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
