<?php


namespace Servebolt\M2Cache\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Servebolt\M2Cache\Helper\TraitHelper;

/**
 * Check when cache should be disabled
 */
class ProcessLogout implements ObserverInterface
{
    use TraitHelper;

    /**
     * Execute method.
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->isCacheEnabled()) {

            return $this;
        }

        $this->getRequestHelper()->markUnsetNoCacheCookie();

        return $this;
    }
}
