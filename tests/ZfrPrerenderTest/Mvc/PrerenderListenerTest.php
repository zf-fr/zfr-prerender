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

namespace ZfrPrerenderTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZfrPrerender\Mvc\PrerenderListener;
use ZfrPrerender\Options\ModuleOptions;
use ZfrPrerenderTest\Util\ServiceManagerFactory;

/**
 * @author Michaël Gallego <mic.gallego@gmail.com>
 *
 * @covers \ZfrPrerender\Mvc\PrerenderListener
 * @group Coverage
 */
class PrerenderListenerTest extends TestCase
{
    public function shouldRenderProvider()
    {
        return array(
            array(
                'user_agent'         => '',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => false
            ),
            // Test a non-bot crawler
            array(
                'user_agent'         => 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => false
            ),
            // Test a Google Bot crawler
            array(
                'user_agent'         => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a Yahoo Bot crawler
            array(
                'user_agent'         => 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a Bing Bot crawler
            array(
                'user_agent'         => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a Baidu Bot crawler
            array(
                'user_agent'         => 'facebookexternalhit/1.1 (+http(s)://www.facebook.com/externalhit_uatext.php)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a Facebook crawler
            array(
                'user_agent'         => 'Twitterbot/1.0',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a Twitter crawler
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a bot crawler with ignored_extension
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/screen.css',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array('.jpg', '.css'),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => false
            ),
            // Test a bot crawler that is whitelisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array('example.com'),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a bot crawler that is whitelisted with more complex regex
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/michael',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array('/users/.*'),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a bot crawler that is not whitelisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array('/bar'),
                'blacklist'          => array(),
                'should_prerender'   => false
            ),
            // Test a bot crawler that is blacklisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array('/foo'),
                'should_prerender'   => false
            ),
            // Test a bot crawler that is blacklisted with more complex regex
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array('/users/*'),
                'should_prerender'   => false
            ),
            // Test a bot crawler that is not blacklisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/bar',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array('/foo'),
                'should_prerender'   => true
            ),
            // Test a bot crawler and a referer that is blacklisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/search',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array('/search'),
                'should_prerender'   => false
            ),
            // Test a bot crawler and a referer that is not blacklisted
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/search',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array(),
                'should_prerender'   => true
            ),
            // Test a bot crawler and a referer that is not blacklisted by a regex
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/profile/search',
                'ignored_extensions' => array(),
                'whitelist'          => array(),
                'blacklist'          => array('^/search', 'help'),
                'should_prerender'   => true
            ),
            // Test a bot crawler that combines whitelist and blacklist (1)
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array('/users/*'),
                'blacklist'          => array('/users/julia'),
                'should_prerender'   => false
            ),
            // Test a bot crawler that combines whitelist and blacklist (2)
            array(
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => array(),
                'whitelist'          => array('/users/*'),
                'blacklist'          => array('/users/michael'),
                'should_prerender'   => true
            ),
        );
    }

    /**
     * @dataProvider shouldRenderProvider
     */
    public function testShouldRender($userAgent, $uri, $referer, $ignoredExtensions, $whitelist, $blacklist, $result)
    {
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');
        $moduleOptions->setIgnoredExtensions($ignoredExtensions);
        $moduleOptions->setWhitelistUrls($whitelist);
        $moduleOptions->setBlacklistUrls($blacklist);

        $request  = new HttpRequest();
        $request->setUri($uri);
        $request->getHeaders()->addHeaderLine('User-Agent', $userAgent)
                              ->addHeaderLine('Referer', $referer);

        $listener = new PrerenderListener($moduleOptions);

        $this->assertEquals($result, $listener->shouldPrerenderPage($request));
    }

    public function testCanDetectedEscapedFragmentQueryParam()
    {
        $request = new HttpRequest();
        $request->getQuery()->set('_escaped_fragment_', 'heyaImABot');

        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');
        $listener      = new PrerenderListener($moduleOptions);

        $this->assertTrue($listener->shouldPrerenderPage($request));
    }

    public function testAttachCorrectly()
    {
        $listener     = new PrerenderListener(new ModuleOptions());
        $eventManager = new EventManager();

        $listener->attach($eventManager);
        $this->assertCount(1, $eventManager->getListeners(MvcEvent::EVENT_ROUTE));
    }

    public function testDoesNothingForNonHttpRequest()
    {
        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest($this->getMock('Zend\Stdlib\RequestInterface'));
        $listener = new PrerenderListener(new ModuleOptions());

        $this->assertNull($listener->prerenderPage($mvcEvent));
    }

    public function testCanGetHttpClient()
    {
        $listener = new PrerenderListener(new ModuleOptions());
        $this->assertInstanceOf('Zend\Http\Client', $listener->getHttpClient());
    }

    public function testCanPerformGetRequest()
    {
        $mvcEvent   = new MvcEvent();
        $request    = new HttpRequest();

        $request->setUri('http://www.example.com');
        $request->getHeaders()->addHeaderLine('User-Agent', 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)');
        $mvcEvent->setRequest($request);

        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');
        $listener      = new PrerenderListener($moduleOptions);

        // Mock the client
        $clientMock = $this->getMock('Zend\Http\Client');
        $clientMock->expects($this->once())
                   ->method('setUri')
                   ->with($moduleOptions->getPrerenderUrl() . '/' . $request->getUriString())
                   ->will($this->returnValue($clientMock));

        $clientMock->expects($this->once())
                   ->method('setMethod')
                   ->with('GET');

        $clientMock->expects($this->once())
                   ->method('send')
                   ->will($this->returnValue($this->getMock('Zend\Stdlib\ResponseInterface')));

        $listener->setHttpClient($clientMock);

        $response = $listener->prerenderPage($mvcEvent);
        $this->assertInstanceOf('Zend\Stdlib\ResponseInterface', $response);
    }
}
