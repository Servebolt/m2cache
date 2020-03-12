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
namespace Servebolt\M2Cache\Plugin\Framework\App;

use Magento\Framework\App\Response\HeaderProvider\HeaderProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class ResponsePlugin
 */
class ResponsePlugin
{
    /**
     * @var \Servebolt\M2Cache\Helper\Request
     */
    private $requestHelper;

    /**
     * ResponsePlugin constructor.
     *
     * @param \Servebolt\M2Cache\Helper\Request $requestHelper
     */
    public function __construct(
        \Servebolt\M2Cache\Helper\Request $requestHelper
    ) {
        $this->requestHelper   = $requestHelper;
    }

    /**
     * @param \Magento\Framework\App\Response\Http $subject
     * @return void
     * @codeCoverageIgnore
     */
    public function afterSendHeaders(\Magento\Framework\App\Response\Http $subject, $result)
    {
        $headersSent = headers_sent();

        if (!$headersSent) {
            $this->getRequestHelper()->processCache();
        }

        return $result;
    }

    /**
     * @return \Servebolt\M2Cache\Helper\Request
     */
    protected function getRequestHelper()
    {
        return $this->requestHelper;
    }
}
