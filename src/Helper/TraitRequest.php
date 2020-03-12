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

use Magento\Framework\App\RequestInterface;

/**
 * Class TraitRequest
 */
trait TraitRequest
{
    /**
    * @param RequestInterface $request
    *
    * @return string
    */
    protected function getRequestHandle(RequestInterface $request)
    {
        $module        = $request->getModuleName();
        $controller    = $request->getControllerName();
        $action        = $request->getActionName();

        return $module . $this->getHandleSeparator() . $controller . $this->getHandleSeparator() . $action;
    }

    /**
     * @param $allowedRequests
     *
     * @return array
     */
    protected function getAllowedHandles($allowedRequests)
    {
        $allowedHandles = [];

        foreach ($allowedRequests as $allowedModule => $allowedControllers) {
            if (!is_array($allowedControllers)) {
                $allowedHandles[] = $allowedModule
                    . $this->getHandleSeparator()
                    . $this->getHandleAnyChar()
                    . $this->getHandleSeparator()
                    . $this->getHandleAnyChar();

                continue;
            }

            foreach ($allowedControllers as $allowedController => $allowedActions) {
                if (!is_array($allowedActions)) {
                    $allowedHandles[] = $allowedModule
                        . $this->getHandleSeparator()
                        . $allowedController
                        . $this->getHandleSeparator()
                        . $this->getHandleAnyChar();

                    continue;
                }

                foreach ($allowedActions as $allowedAction => $unused) {
                    $allowedHandles[] = $allowedModule
                        . $this->getHandleSeparator()
                        . $allowedController
                        . $this->getHandleSeparator()
                        . $allowedAction;
                }
            }
        }

        return $allowedHandles;
    }

    /**
     * @return string
     */
    private function getHandleSeparator()
    {
        return '_';
    }

    /**
     * @return string
     */
    private function getHandleAnyChar()
    {
        return '.*';
    }
}
