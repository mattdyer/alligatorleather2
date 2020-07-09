<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */

namespace Magetrend\GiftCard\Plugin\Framework\Mail;

/**
 * MimeMessage Plugin class
 */
class MimeMessage
{
    /**
     * @var \Magetrend\EmailAttachment\Helper\Data|\Magetrend\GiftCard\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magetrend\GiftCard\Model\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * TransportInterfaceFactory constructor.
     * @param \Magetrend\EmailAttachment\Model\AttachmentManager $attachmentManager
     * @param \Magetrend\EmailAttachment\Helper\Data $moduleHelper
     */
    public function __construct(
        \Magetrend\GiftCard\Helper\Data $moduleHelper,
        \Magetrend\GiftCard\Model\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->transportBuilder = $transportBuilder;
        $this->objectManager = $objectManager;
    }

    /**
     * Hook
     * @param $subject
     * @param array $data
     * @return array
     */
    public function afterGetParts($subject, $parts)
    {
        if (!$this->moduleHelper->isActive()) {
            return $parts;
        }

        if (!empty($parts)) {
            $pdfs = $this->transportBuilder->getPdf();
            if (!empty($pdfs)) {
                $type = 'application/pdf';
                $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                $encoding = \Zend_Mime::ENCODING_BASE64;

                foreach ($pdfs as $pdf) {
                    $content = file_get_contents($pdf['path']);
                    $attachment = $this->objectManager->create(
                        'Magento\Framework\Mail\MimePartInterface',
                        [
                            'content' => $content,
                            'type' => $type,
                            'fileName' => $pdf['name'],
                            'disposition' => $disposition,
                            'encoding' => $encoding
                        ]
                    );

                    $parts[] = $attachment;
                }
            }
        }

        return $parts;
    }
}
