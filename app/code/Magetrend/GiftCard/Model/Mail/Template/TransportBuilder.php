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

namespace Magetrend\GiftCard\Model\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{

    private $pdf = [];

    public function addPdf($filePath, $name)
    {
        $this->pdf[] = [
            'path' => $filePath,
            'name' => $name
        ];

        return $this;
    }

    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Create new message
     *
     * @return $this
     */
    public function createMessage()
    {
        $this->message = $this->objectManager->create('Magento\Framework\Mail\Message');
        return $this;
    }

    protected function prepareMessage()
    {
        parent::prepareMessage();
        if (!empty($this->pdf)) {
            $type = 'application/pdf';
            $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
            $encoding = \Zend_Mime::ENCODING_BASE64;
            foreach ($this->pdf as $pdf) {
                $content = file_get_contents($pdf['path']);
                if (method_exists($this->message, 'createAttachment')) {
                    $this->message->createAttachment($content, $type, $disposition, $encoding, $pdf['name']);
                } elseif ($this->message->getBody() instanceof \Zend\Mime\Message) {
                    $body = $this->message->getBody();
                    $attachment = new \Zend\Mime\Part($content);
                    $attachment->type = $type;
                    $attachment->filename = $pdf['name'];
                    $attachment->disposition = $disposition;
                    $attachment->encoding = $encoding;
                    $body->addPart($attachment);
                    $this->message->setBody($body);
                }
            }
        }
        return $this;
    }
}
