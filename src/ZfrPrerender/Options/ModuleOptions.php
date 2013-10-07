<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfrPrerender\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * @author Michaël Gallego
 * @licence MIT
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $prerenderUrl;

    /**
     * @var array
     */
    protected $crawlerUserAgents = array();

    /**
     * @var array
     */
    protected $ignoredExtensions = array();

    /**
     * @var array
     */
    protected $whitelistUrls = array();

    /**
     * @var array
     */
    protected $blacklistUrls = array();

    /**
     * Set the prerender service URL
     *
     * @param string $prerenderUrl
     * @return void
     */
    public function setPrerenderUrl($prerenderUrl)
    {
        $this->prerenderUrl = (string) $prerenderUrl;
    }

    /**
     * Get the prerender service URL
     *
     * @return string
     */
    public function getPrerenderUrl()
    {
        return $this->prerenderUrl;
    }

    /**
     * Set the crawler user agents
     *
     * @param  array $crawlerUserAgents
     * @return void
     */
    public function setCrawlerUserAgents(array $crawlerUserAgents)
    {
        $this->crawlerUserAgents = $crawlerUserAgents;
    }

    /**
     * Get the crawler user agents
     *
     * @return array
     */
    public function getCrawlerUserAgents()
    {
        return $this->crawlerUserAgents;
    }

    /**
     * Set the ignored extensions
     *
     * @param  array $ignoredExtensions
     * @return void
     */
    public function setIgnoredExtensions(array $ignoredExtensions)
    {
        $this->ignoredExtensions = $ignoredExtensions;
    }

    /**
     * Get the ignored extensions
     *
     * @return array
     */
    public function getIgnoredExtensions()
    {
        return $this->ignoredExtensions;
    }

    /**
     * Set the whitelist URLs
     *
     * @param array $whitelistUrls
     * @return void
     */
    public function setWhitelistUrls(array $whitelistUrls)
    {
        $this->whitelistUrls = $whitelistUrls;
    }

    /**
     * Get the whitelist URLs
     *
     * @return array
     */
    public function getWhitelistUrls()
    {
        return $this->whitelistUrls;
    }

    /**
     * Set the blacklist URLs
     *
     * @param array $blacklistUrls
     * @return void
     */
    public function setBlacklistUrls(array $blacklistUrls)
    {
        $this->blacklistUrls = $blacklistUrls;
    }

    /**
     * Get the blacklist URLs
     *
     * @return array
     */
    public function getBlacklistUrls()
    {
        return $this->blacklistUrls;
    }
}
