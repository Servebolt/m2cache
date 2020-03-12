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

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class Config
 * 
 * Config helper
 */
class Config
{
    /** Paths to external cache config options */
    const XPATH_MODULE         = 'system/full_page_cache/servebolt_fpc';
    const XPATH_MODULE_ENABLED = 'enabled';

    /** Identifier for full page cache */
    const FPC_CACHE_ID = 2453;
    
    /** Cookies settings */
    const XPATH_CACHE_COOKIES            = 'cookies';
    const XPATH_CACHE_CACHEABLE_REQUESTS = 'allowed_requests';
    const XPATH_CACHE_DISALLOWED_HANDLES = 'disallowed_handles';
    
    /** Headers settings */
    const XPATH_CACHE_HEADERS            = 'headers';
    const XPATH_CACHE_HEADERS_EXPIRES    = 'expires';
    
    const XPATH_LIFETIME = 'lifetime';
    
    /** Debugging settings */
    const XPATH_DEBUGGING         = 'debugging';
    const XPATH_DEBUGGING_ENABLED = 'enabled';
    const XPATH_DEBUGGING_COOKIES = 'cookies';
    
    /** Debugging settings */
    const XPATH_FORMKEY_BYPASS          = 'formkey_bypass';
    const XPATH_FORMKEY_BYPASS_ENABLED  = 'enabled';
    const XPATH_FORMKEY_BYPASS_REQUESTS = 'bypass_requests';

    /** Cookie name for disabling external caching */
    const NO_CACHE_COOKIE = 'no_cache';
    /** Header name for cache validity */
    const EXPIRES_HEADER = 'Expires';

    protected $enabled;
    protected $debuggingEnabled;
    protected $formkeyEnabled;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var WriterInterface
     */
    private $storeWriter;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $cacheTypeList;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface                           $scopeConfig
     * @param WriterInterface                                $storeWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $storeWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeWriter = $storeWriter;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Check whether external cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (is_null($this->enabled)) {
            $this->enabled = $this->getConfigFlag($this::XPATH_MODULE_ENABLED);
        }
        
        return $this->enabled;
    }

    /**
     * Disable cache
     *
     * @return bool
     */
    public function disable()
    {
        $this->storeWriter->save($this::XPATH_MODULE . '/' . $this::XPATH_MODULE_ENABLED, 0);
        $this->enabled = false;
        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }

    /**
     * Check whether debugging mode is enabled
     *
     * @return bool
     */
    public function isDebuggingEnabled()
    {
        if (is_null($this->debuggingEnabled)) {
            $this->debuggingEnabled = (bool) trim($this->getDebuggingConfig($this::XPATH_DEBUGGING_ENABLED));
        }
        
        return $this->debuggingEnabled;
    }

    /**
     * Check whether formkey bypassing mode is enabled
     *
     * @todo check return type
     * @return bool
     */
    public function isFormkeyValidationEnabled()
    {
        if (is_null($this->formkeyEnabled)) {
            $this->formkeyEnabled = (bool) trim($this->getFormkeyConfig($this::XPATH_FORMKEY_BYPASS_ENABLED));
        }
        
        return $this->formkeyEnabled;
    }

    /**
     * Return debugging cookies
     *
     * @return array
     */
    public function getDebuggingCookies()
    {
        return trim($this->getDebuggingConfig($this::XPATH_DEBUGGING_COOKIES));
    }

    /**
     * Returns a lifetime of no-cache cookies
     *
     * @return string Seconds
     */
    public function getNoCacheCookieLifetime()
    {
        $configuredCookie = $this->getCookieConfig($this->getNoCacheCookieName(), $this::XPATH_LIFETIME);

        return $configuredCookie ?: null;//Mage::getModel('core/cookie')->getLifetime();
    }

    /**
     * Returns a lifetime of Expires header
     *
     * @return string Seconds
     */
    public function getExpiresHeaderLifetime()
    {
        $configuredHeader = $this->getHeaderConfig($this::XPATH_CACHE_HEADERS_EXPIRES, $this::XPATH_LIFETIME);

        return $configuredHeader ?: null;
    }

    /**
     * Returns tree of cacheable requests
     *
     * -> module
     * ---> controller1
     * ---> controller2
     * -----> action1
     * -----> action2
     *
     * @return string[]
     */
    public function getCacheableRequests()
    {
        return $this->getConfig($this::XPATH_CACHE_CACHEABLE_REQUESTS);
    }

    /**
     * @return string[]
     */
    public function getDisallowedHandles()
    {
        $handles = $this->getConfig($this::XPATH_CACHE_DISALLOWED_HANDLES);
        
        return is_array($handles) ? array_keys($handles) : [];
    }

    /**
     * @return string
     */
    public function getNoCacheCookieName()
    {
        return $this::NO_CACHE_COOKIE;
    }

    /**
     * @return string
     */
    public function getExpiresHeaderName()
    {
        return $this::EXPIRES_HEADER;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    protected function getConfigFlag($path)
    {
        return $this->scopeConfig->isSetFlag($this::XPATH_MODULE . '/' . $path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getConfig($path)
    {
        return $this->scopeConfig->getValue($this::XPATH_MODULE . '/' . $path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    protected function getDebuggingConfig($path)
    {
        return $this->getConfig($this::XPATH_DEBUGGING . '/' . $path);
    }

    /**
     * @param $cookieName
     * @param $configType
     *
     * @return mixed
     */
    protected function getCookieConfig($cookieName, $configType)
    {
        return $this->getConfig($this::XPATH_CACHE_COOKIES . '/' . $cookieName . '/' . $configType);
    }

    /**
     * @param $headerName
     * @param $configType
     *
     * @return mixed
     */
    protected function getHeaderConfig($headerName, $configType)
    {
        return $this->getConfig($this::XPATH_CACHE_HEADERS . '/' . $headerName . '/' . $configType);
    }

    /**
     * Check whether formkey bypassing mode is enabled
     *
     * @param $path
     *
     * @return array
     */
    public function getFormkeyConfig($path)
    {
        return $this->getConfig($this::XPATH_FORMKEY_BYPASS . '/' . $path);
    }

    /**
     * Returns tree of cacheable for which bypassing form key should be applied
     *
     * -> module
     * ---> controller1
     * ---> controller2
     * -----> action1
     * -----> action2
     *
     * @return string[]
     */
    public function getFormkeyBypassRequests()
    {
        return $this->getFormkeyConfig($this::XPATH_FORMKEY_BYPASS_REQUESTS);
    }
}
