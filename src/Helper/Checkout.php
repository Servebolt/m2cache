<?php
/**
 * Copyright (c) 2019 Servebolt
 *
 * Servebolt  reserves all rights in the Program as delivered. The Program
 * or any portion thereof may not be reproduced in any form whatsoever without
 * the written consent of Servebolt, except as provided by licence. A licence
 * under Servebolt's rights in the Program may be available directly from
 * Servebolt.
 *
 * Disclaimer:
 * THIS NOTICE MAY NOT BE REMOVED FROM THE PROGRAM BY ANY USER THEREOF.
 * THE PROGRAM IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE PROGRAM OR THE USE OR OTHER DEALINGS
 * IN THE PROGRAM.
 *
 * @category    Servebolt
 * @package     Servebolt_M2Cache
 * @copyright   Copyright (c) 2019 Servebolt

 */

namespace Servebolt\M2Cache\Helper;

use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote;

/**
 * Class Checkout
 * 
 * Request helper
*/
class Checkout
{
    protected $hadItems;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * Checkout constructor.
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Request\Proxy         $requestHelper,
        CheckoutSession\Proxy $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->requestHelper   = $requestHelper;
    }

    /**
     * Mark checkout data as cacheable or not
     */
    public function markCheckout()
    {
        if ($this->hasItems()) {
            $this->getRequestHelper()->markBypassCache();
        } elseif ($this->hadItems()) {
            $this->getRequestHelper()->markUnsetNoCacheCookie();
        }
    }

    /**
     * Stores info about cart for later processing
     */
    public function controlCheckout()
    {
        $this->hadItems = $this->hasItems();
    }

    /**
     * Checks if quote is cacheable
     *
     * Quote is cacheable if it has no items
     *
     * @return bool
     */
    protected function isCacheable()
    {
        return !$this->hasItems();
    }

    /**
     * @return CheckoutSession
     */
    protected function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @return bool
     */
    protected function hasItems()
    {
        /** @var Quote $quote */
        $quote = $this->getCheckoutSession()->getQuote();

        return $quote->hasItems();
    }

    /**
     * @return bool
     */
    protected function hadItems()
    {
        return (bool) $this->hadItems;
    }

    /**
     * @return Request
     */
    private function getRequestHelper()
    {
        return $this->requestHelper;
    }

}
