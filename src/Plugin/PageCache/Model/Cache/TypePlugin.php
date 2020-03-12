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
namespace Servebolt\M2Cache\Plugin\PageCache\Model\Cache;

use Magento\PageCache\Model\Config;
use Servebolt\M2Cache\Helper\Config as ConfigHelper;
use Magento\Framework\App\PageCache\Kernel;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Zend\Http\Header\HeaderInterface as HttpHeaderInterface;
use Magento\PageCache\Model\Cache\Type as CacheType;

/**
 * Plugin for processing builtin cache
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TypePlugin
{

    /**
     * @var \Servebolt\M2Cache\Helper\Request
     */
    private $requestHelper;

    /**
     * TypePlugin constructor.
     *
     * @param \Servebolt\M2Cache\Helper\Request $requestHelper
     */
    public function __construct(\Servebolt\M2Cache\Helper\Request $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param ResponseHttp $response
     * @return ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(\Magento\PageCache\Model\Cache\Type $subject, $result)
    {
        $this->requestHelper->markCacheable();

        return $result;
    }
}
