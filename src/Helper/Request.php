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


use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Class Request
 *
 * Request helper
 */
class Request
{
    use TraitRequest;

    use TraitHelper {
        TraitHelper::__construct as private __traitHelperConstruct;
    }

    const REGISTER_KEY_CACHEABLE              = 'Servebolt_m2cache_cacheable';

    const REGISTER_KEY_NOT_CACHEABLE          = 'Servebolt_m2cache_not_cacheable';

    const REGISTER_KEY_BYPASS                 = 'Servebolt_m2cache_bypass';

    const REGISTER_KEY_DELETE_NO_CACHE_COOKIE = 'Servebolt_m2cache_delete_no_cache';

    const REGISTER_KEY_HAS_MESSAGES           = 'Servebolt_m2cache_has_messages';

    const HEADER_NAME_CACHE_CONTROL           = 'Cache-Control';

    const HEADER_NAME_PRAGMA                  = 'Pragma';

    const HEADER_NAME_EXPIRE                  = 'Expires';

    const HEADER_NAME_SET_COOKIE              = 'Set-Cookie';

    const TEMPORARY_NO_CACHE_COOKIE_LIFETIME  = 5;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CookieManagerInterface|PhpCookieManager
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * Request constructor.
     *
     * @param                             $configHelper
     * @param                             $requestHelper
     * @param                             $layoutHelper
     * @param                             $debuggingHelper
     * @param                             $checkoutHelper
     * @param Registry                    $registry
     * @param CookieManagerInterface      $cookieManager
     * @param CookieMetadataFactory       $cookieMetadataFactory
     * @param SessionManagerInterface     $sessionManager
     * @param HttpRequest                 $request
     */
    public function __construct(
        Config\Proxy    $configHelper,
        Request\Proxy   $requestHelper,
        Layout\Proxy    $layoutHelper,
        Debug\Proxy     $debuggingHelper,
        Checkout\Proxy  $checkoutHelper,
        Registry $registry,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        HttpRequest\Proxy $request
    ) {
        $this->__traitHelperConstruct(
            $configHelper,
            $requestHelper,
            $layoutHelper,
            $debuggingHelper,
            $checkoutHelper
        );

        $this->registry              = $registry;
        $this->cookieManager         = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager        = $sessionManager;
        $this->request               = $request;
    }

    /**
     * Marks request as cacheable
     */
    public function markCacheable()
    {
        $this->registry->register($this::REGISTER_KEY_CACHEABLE, 1, true);
    }

    /**
     * Marks request as not cacheable
     */
    public function markNotCacheable()
    {
        $this->registry->unregister($this::REGISTER_KEY_CACHEABLE);
        $this->registry->register($this::REGISTER_KEY_NOT_CACHEABLE, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedCacheable()
    {
        return (bool)$this->registry->registry($this::REGISTER_KEY_CACHEABLE) && !$this->isMarkedNotCacheable();
    }

    /**
     * @return bool
     */
    public function isMarkedNotCacheable()
    {
        return (bool)$this->registry->registry($this::REGISTER_KEY_NOT_CACHEABLE);
    }

    /**
     * Marks request to delete no-cache cookie
     */
    public function markUnsetNoCacheCookie()
    {
        $this->registry->register($this::REGISTER_KEY_DELETE_NO_CACHE_COOKIE, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedUnsetNoCacheCookie()
    {
        return (bool)$this->registry->registry($this::REGISTER_KEY_DELETE_NO_CACHE_COOKIE);
    }

    /**
     * Marks request as cacheable
     */
    public function markBypassCache()
    {
        $this->registry->register($this::REGISTER_KEY_BYPASS, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedBypassCache()
    {
        return (bool)$this->registry->registry($this::REGISTER_KEY_BYPASS);
    }

    /**
     * Marks response as having messages to be outputted
     */
    public function markHasMessages()
    {
        $this->registry->register($this::REGISTER_KEY_HAS_MESSAGES, 1, true);
    }

    /**
     * @return bool
     */
    public function isMarkedHasMessages()
    {
        return (bool) $this->registry->registry($this::REGISTER_KEY_HAS_MESSAGES);
    }

    /**
     * Disable caching on external storage by setting special cookie
     *
     * @param int $noCacheCookieLifetime
     *
     * @return void
     */
    public function setNoCacheCookie($noCacheCookieLifetime = null)
    {
        $noCacheCookieName     = $this->getConfigHelper()->getNoCacheCookieName();
        $noCacheCookieLifetime = $noCacheCookieLifetime ?? $this->getConfigHelper()->getNoCacheCookieLifetime();

        $noCache = $this->getCookieManager()->getCookie($noCacheCookieName);

        /** @var PublicCookieMetadata $cookieMetadata */
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $cookieMetadata->setDuration($noCacheCookieLifetime);
        $cookieMetadata->setPath($this->sessionManager->getCookiePath());
        $cookieMetadata->setDomain($this->sessionManager->getCookieDomain());

        $this->getCookieManager()->setPublicCookie($noCacheCookieName, $noCache, $cookieMetadata);

//        if ($noCache) {
//            $this->getCookieManager()->renew($noCacheCookieName, $noCacheCookieLifetime);
//        } else {
//            $this->getCookieManager()->set($noCacheCookieName, 1, $noCacheCookieLifetime);
//        }
    }

    /**
     * Disable temporarly caching on external storage by setting special cookie
     *
     * @return void
     */
    public function setTemporaryNoCacheCookie()
    {
        $this->setNoCacheCookie($this::TEMPORARY_NO_CACHE_COOKIE_LIFETIME);
    }

    /**
     * Set caching validity for external storage
     *
     * @return void
     */
    public function setExpiresHeader()
    {
        $expiresHeaderName     = $this->getConfigHelper()->getExpiresHeaderName();
        $expiresHeaderLifetime = $this->getConfigHelper()->getExpiresHeaderLifetime();

        $this->setHeader($expiresHeaderName, gmdate('D, d M Y H:i:s \G\M\T', time() + $expiresHeaderLifetime));
    }

    /**
     * Remove no-cache cookie
     *
     * @return void
     */
    public function unsetNoCacheCookie()
    {
        $this->getCookieManager()->deleteCookie($this->getConfigHelper()->getNoCacheCookieName());
    }

    /**
     * Remove all cookies
     *
     * @return void
     */
    public function unsetAllCookies()
    {
        $this->unsetHeader($this::HEADER_NAME_SET_COOKIE);
    }

    /**
     * @param RequestInterface $request
     */
    public function markRequest(RequestInterface $request)
    {
        $this->isCacheable($request) ? $this->markCacheable() : $this->markNotCacheable();
    }

    /**
     * @param RequestInterface $request
     */
    public function addRandomUrlParameter(RequestInterface $request)
    {
        $this->isCacheable($request) ? $this->markCacheable() : $this->markNotCacheable();
    }

    /**
     *
     */
    public function processCache()
    {
        $this->getDebugHelper()->logDebugInfo();

        if ($this->isMarkedBypassCache()) {
            $this->setNoCacheCookie();
        } elseif ($this->isMarkedHasMessages()) {
            $this->setTemporaryNoCacheCookie();
        } elseif ($this->isMarkedCacheable()) {
            $this->setExpiresHeader();
            $this->unsetCacheControlHeader();
            $this->unsetPragmaHeader();
            $this->unsetAllCookies();
        }

        if (!$this->isMarkedHasMessages() && $this->isMarkedUnsetNoCacheCookie()) {
            $this->unsetNoCacheCookie();
        }

        $this->getDebugHelper()->logDebugInfo();
    }

    /**
     * @return string
     */
    public function getUriString()
    {
        return $this->request->getUriString();
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    protected function isCacheable(RequestInterface $request)
    {
        if ($request->isPost()) {

            return false;
        }

        $allowedRequests = $this->getConfigHelper()->getCacheableRequests();

        if (!$allowedRequests) {

            return false;
        }

        $requestString  = $this->getRequestHandle($request);
        $allowedHandles = $this->getAllowedHandles($allowedRequests);

        foreach ($allowedHandles as $allowedHandle) {
            if (preg_match('/(' . $allowedHandle . ')/mi', $requestString)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @return CookieManagerInterface|PhpCookieManager
     */
    protected function getCookieManager()
    {
        return $this->cookieManager;
    }

    /**
     * Removes Cache-Control header
     */
    protected function unsetCacheControlHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_CACHE_CONTROL);
    }

    /**
     * Removes Expires header
     */
    protected function unsetExpiresHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_EXPIRE);
    }

    /**
     * Removes Pragma header
     */
    protected function unsetPragmaHeader()
    {
        $this->unsetHeader($this::HEADER_NAME_PRAGMA);
    }

    /**
     * @param $headerName
     */
    protected function unsetHeader($headerName)
    {
        header_remove($headerName);
    }

    /**
     * @param $headerName
     */
    protected function setHeader(
        $headerName,
        $headerValue
    ) {
        header($headerName . ': ' . $headerValue, true);
    }
}
