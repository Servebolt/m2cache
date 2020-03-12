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


use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Layout
 * 
 * Layout helper
*/
class Layout
{

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * Debug constructor.
     *
     * @param Config\Proxy    $configHelper
     * @param Request\Proxy   $requestHelper
     * @param LayoutInterface $layout
     */
    public function __construct(
        Config\Proxy  $configHelper,
        Request\Proxy $requestHelper,
        LayoutInterface $layout
    ) {

        $this->configHelper  = $configHelper;
        $this->requestHelper = $requestHelper;
        $this->layout        = $layout;
    }

    /**
     * Mark layout as cacheable or not
     */
    public function markLayout()
    {
        $this->isHandleCacheable() ? : $this->getRequestHelper()->markBypassCache();
    }
        
    /**
     * @return bool
     */
    protected function isHandleCacheable()
    {
        $update = $this->getLayoutUpdate();

        if ($update) {
            $updateHandles = $update->getHandles();

            if (!empty($updateHandles)) {
                $disallowedHandles = $this->getConfigHelper()->getDisallowedHandles();

                if (!empty($disallowedHandles)) {
                    $disallowedHandlesInLayout = array_intersect($updateHandles, $disallowedHandles);

                    return empty($disallowedHandlesInLayout);
                }
            }
        }

        return true;
    }
    
    /**
     * @return ProcessorInterface
     */
    protected function getLayoutUpdate() 
    {
        return $this->getLayout()->getUpdate();
    }

    /**
     * Retrieve layout model object
     *
     * @return LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return Config
     */
    protected function getConfigHelper(): Config
    {
        return $this->configHelper;
    }

    /**
     * @return Request
     */
    protected function getRequestHelper(): Request
    {
        return $this->requestHelper;
    }
}
