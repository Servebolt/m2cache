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
 * @package     Servebolt_M2Cache_Model_Observer
 * @copyright   Copyright (c) 2019 Servebolt

 */

namespace Servebolt\M2Cache\Helper;

/**
 * Class TraitHelper
 */
trait TraitHelper
{
    /** @var Config */
    protected $configHelper;

    /** @var Request */
    protected $requestHelper;

    /** @var Layout */
    protected $layoutHelper;
 
    /** @var Debug */
    protected $debuggingHelper;
 
    /** @var Checkout */
    protected $checkoutHelper;

    /**
     * TraitHelper constructor.
     *
     * @param Config   $configHelper
     * @param Request  $requestHelper
     * @param Layout   $layoutHelper
     * @param Debug    $debuggingHelper
     * @param Checkout $checkoutHelper
     */
    public function __construct(
        Config\Proxy    $configHelper,
        Request\Proxy   $requestHelper,
        Layout\Proxy    $layoutHelper,
        Debug\Proxy     $debuggingHelper,
        Checkout\Proxy  $checkoutHelper
    ){
        $this->configHelper    = $configHelper;
        $this->requestHelper   = $requestHelper;
        $this->layoutHelper    = $layoutHelper;
        $this->debuggingHelper = $debuggingHelper;
        $this->checkoutHelper  = $checkoutHelper;
    }

    /**
     * Check if cache cookies are enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->getConfigHelper()->isEnabled();
    }


    /**
     * @return Config
     */
    protected function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @return Request
     */
    protected function getRequestHelper()
    {
        return $this->requestHelper;
    }

    /**
     * @return Layout
     */
    protected function getLayoutHelper()
    {
        return $this->layoutHelper;
    }

    /**
     * @return Debug
     */
    protected function getDebugHelper()
    {
        return $this->debuggingHelper;
    }

    /**
     * @return Checkout
     */
    protected function getCheckoutHelper()
    {
        return $this->checkoutHelper;
    }
}
