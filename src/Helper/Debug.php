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

use Psr\Log\LoggerInterface;

/**
 * Class Debug
 * 
 * Debug helper
 */
class Debug
{
    protected $requestId;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Debug constructor.
     *
     * @param Config\Proxy    $configHelper
     * @param Request\Proxy   $requestHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config\Proxy  $configHelper,
        Request\Proxy $requestHelper,
        LoggerInterface $logger
    ) {
        $this->configHelper  = $configHelper;
        $this->requestHelper = $requestHelper;
        $this->logger        = $logger;
    }

    /**
     * Check whether debugging mode is enabled
     *
     * @return bool
     */
    public function isDebuggingEnabled()
    {
        return $this->getConfigHelper()->isDebuggingEnabled();
    }
    
    /**
     * Logs response information to file
     */
    public function logDebugInfo() 
    {
        if ($this->isDebuggingEnabled()) {
            $messages = [];
            $messages['ID']         = $this->getRequestId();
            $messages['url']        = $this->getRequestHelper()->getUriString();
            $messages['bypass']     = $this->getRequestHelper()->isMarkedBypassCache();
            $messages['cacheable']  = $this->getRequestHelper()->isMarkedCacheable();            
            $messages['headers']    = $this->getHeaders();
            
            $this->logger->debug(var_export($messages, true));
        }
    }

    /**
     * Get headers
     * 
     * @return array
     */
    protected function getHeaders()
    {
        return headers_list();
    }

    /**
     * @return mixed
     */
    public function getRequestId()
    {
        if (is_null($this->requestId)) {
            $this->requestId = date('H:i:s');
        }
        
        return $this->requestId;
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
