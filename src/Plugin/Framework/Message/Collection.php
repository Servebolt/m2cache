<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Servebolt\M2Cache\Plugin\Framework\Message;

use \Magento\Framework\Message\Collection as MessageCollection;
use Magento\Framework\Message\MessageInterface;
use \Servebolt\M2Cache\Helper\TraitHelper;

/**
 * Class Collection
 *
 * @package Servebolt_M2Cache
 */
class Collection
{
    use TraitHelper;

    /**
     * @param MessageCollection $messageCollection
     * @param MessageInterface  $message
     *
     * @return null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAddMessage(MessageCollection $messageCollection, MessageInterface $message)
    {
        if (!$this->isCacheEnabled()) {

            return null;
        }

        $this->getRequestHelper()->markHasMessages();

        return null;
    }

    /**
     * @param MessageCollection $messageCollection
     *
     * @return null
     */
    public function beforeClear(MessageCollection $messageCollection)
    {
        if (!$this->isCacheEnabled()) {

            return null;
        }

        if ($messageCollection->getCount()) {
            $this->getRequestHelper()->markHasMessages();
        }

        return null;
    }
}
