<?php


namespace Servebolt\M2Cache\Observer\Adminhtml\System;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Servebolt\M2Cache\Helper\TraitHelper;
use \Magento\Store\Model\ScopeInterface;

/**
 * Disable Servebolt if FPC is disabled
 */
class ConfigChange implements ObserverInterface
{
    /**
     * @var \Servebolt\M2Cache\Helper\Config\Proxy
     */
    protected $configHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigChange constructor.
     *
     * @param \Servebolt\M2Cache\Helper\Config            $configHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Servebolt\M2Cache\Helper\Config $configHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
         $this->configHelper = $configHelper;
         $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute method.
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isEnabled()) {

            return $this;
        }

        /** @var  $event */
        $event = $observer->getEvent();
        
        $pageCacheType = $this->scopeConfig->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);
        
        if ($pageCacheType != \Magento\PageCache\Model\Config::BUILT_IN) {
            $this->configHelper->disable();
        }
        
        return $this;
    }
}
