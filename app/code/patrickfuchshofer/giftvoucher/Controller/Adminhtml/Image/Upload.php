<?php
/*
 * Patrickfuchshofer_Giftvoucher

 * @category   Patrickfuchshofer
 * @package    Patrickfuchshofer_Giftvoucher
 * @copyright  Copyright (c) 2017 Patrickfuchshofer
 * @license    https://github.com/turiknox/magento2-sample-imageuploader/blob/master/LICENSE.md
 * @version    1.0.0
 */
namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Patrickfuchshofer\Giftvoucher\Model\Uploader;

class Upload extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Patrickfuchshofer_Giftvoucher::image';

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param Uploader $uploader
     */
    public function __construct(
        Context $context,
        Uploader $uploader
    ) {
        parent::__construct($context);
        $this->uploader = $uploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $_files = $this->getRequest()->getFiles();


        $field = 'image';

        if(isset($_files['image1'])){
            $field = 'image1';
        }
        if(isset($_files['image2'])){
            $field = 'image2';
        }
        if(isset($_files['image3'])){
            $field = 'image3';
        }
        try {
            $result = $this->uploader->saveFileToTmpDir($field);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
