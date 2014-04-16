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
use Zend\EventManager\ResponseCollection;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZfrPrerender\Mvc\PrerenderEvent;
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
        return [
            [
                'user_agent'         => '',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => false
            ],
            // Test a non-bot crawler
            [
                'user_agent'         => 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => false
            ],
            // Test a Baidu Bot crawler
            [
                'user_agent'         => 'facebookexternalhit/1.1 (+http(s)://www.facebook.com/externalhit_uatext.php)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a Facebook crawler
            [
                'user_agent'         => 'Twitterbot/1.0',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a Twitter crawler
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a bot crawler with ignored_extension
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/screen.css',
                'referer'            => 'http://google.com',
                'ignored_extensions' => ['.jpg', '.css'],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => false
            ],
            // Test a bot crawler that is whitelisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => ['example.com'],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a bot crawler that is whitelisted with more complex regex
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/michael',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => ['/users/.*'],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a bot crawler that is not whitelisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => ['/bar'],
                'blacklist'          => [],
                'should_prerender'   => false
            ],
            // Test a bot crawler that is blacklisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => ['/foo'],
                'should_prerender'   => false
            ],
            // Test a bot crawler that is blacklisted with more complex regex
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => ['/users/*'],
                'should_prerender'   => false
            ],
            // Test a bot crawler that is not blacklisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/bar',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => ['/foo'],
                'should_prerender'   => true
            ],
            // Test a bot crawler and a referer that is blacklisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/search',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => ['/search'],
                'should_prerender'   => false
            ],
            // Test a bot crawler and a referer that is not blacklisted
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/search',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => [],
                'should_prerender'   => true
            ],
            // Test a bot crawler and a referer that is not blacklisted by a regex
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/foo',
                'referer'            => '/profile/search',
                'ignored_extensions' => [],
                'whitelist'          => [],
                'blacklist'          => ['^/search', 'help'],
                'should_prerender'   => true
            ],
            // Test a bot crawler that combines whitelist and blacklist (1)
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => ['/users/*'],
                'blacklist'          => ['/users/julia'],
                'should_prerender'   => false
            ],
            // Test a bot crawler that combines whitelist and blacklist (2)
            [
                'user_agent'         => 'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                'uri'                => 'http://www.example.com/users/julia',
                'referer'            => 'http://google.com',
                'ignored_extensions' => [],
                'whitelist'          => ['/users/*'],
                'blacklist'          => ['/users/michael'],
                'should_prerender'   => true
            ],
        ];
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
        $request->getHeaders()->addHeaderLine('User-Agent', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)');
        $mvcEvent->setRequest($request);

        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');
        $listener      = new PrerenderListener($moduleOptions);

        $prerenderRequest = new HttpRequest();
        $response         = $this->getMock('Zend\Http\Response');

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
                   ->method('getRequest')
                   ->will($this->returnValue($prerenderRequest));

        $clientMock->expects($this->once())
                   ->method('send')
                   ->with($this->callback(function(HttpRequest $request) {
                $headers = $request->getHeaders();

                $this->assertEquals(
                    'Baiduspider+(+http://www.baidu.com/search/spider.htm)',
                    $headers->get('User-Agent')->getFieldValue()
                );
                $this->assertEquals('gzip', $headers->get('Accept-Encoding')->getFieldValue());

                return true;
            }))
                   ->will($this->returnValue($response));

        $listener->setHttpClient($clientMock);

        $response = $listener->prerenderPage($mvcEvent);
        $this->assertInstanceOf('Zend\Stdlib\ResponseInterface', $response);
    }

    public function testSetCorrectIdentifiers()
    {
        $listener = new PrerenderListener(new ModuleOptions());
        $listener->setEventManager(new EventManager());

        $eventManager = $listener->getEventManager();

        $this->assertEquals(['ZfrPrerender\Mvc\PrerenderListener'], $eventManager->getIdentifiers());
    }

    public function testTriggerEventsAndStopIfResponseIsReturned()
    {
        $mvcEvent   = new MvcEvent();
        $request    = new HttpRequest();

        $request->setUri('http://www.example.com');
        $request->getHeaders()->addHeaderLine('User-Agent', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)');
        $mvcEvent->setRequest($request);

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');

        $listener = new PrerenderListener($moduleOptions);
        $listener->setEventManager($eventManager);

        $response           = $this->getMock('Zend\Http\Response');
        $responseCollection = new ResponseCollection();
        $responseCollection->push($response);

        $eventManager->expects($this->once())
                     ->method('trigger')
                     ->with(PrerenderEvent::EVENT_PRERENDER_PRE)
                     ->will($this->returnValue($responseCollection));

        $this->assertSame($response, $listener->prerenderPage($mvcEvent));
    }

    public function testAddHeaderIfTokenIsSpecified()
    {
        $mvcEvent   = new MvcEvent();
        $request    = new HttpRequest();

        $request->setUri('http://www.example.com');
        $request->getHeaders()->addHeaderLine('User-Agent', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)');
        $mvcEvent->setRequest($request);

        /** @var \ZfrPrerender\Options\ModuleOptions $moduleOptions */
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');
        $moduleOptions->setPrerenderToken('abc');

        $prerenderRequest = new HttpRequest();
        $listener         = new PrerenderListener($moduleOptions);

        $response = $this->getMock('Zend\Http\Response');

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
                   ->will($this->returnValue($response));

        $clientMock->expects($this->once())
                   ->method('getRequest')
                   ->will($this->returnValue($prerenderRequest));

        $listener->setHttpClient($clientMock);

        $listener->prerenderPage($mvcEvent);

        $this->assertTrue($prerenderRequest->getHeaders()->has('X-Prerender-Token'));
        $this->assertEquals(
            $prerenderRequest->getHeader('X-Prerender-Token')->getFieldValue(),
            $moduleOptions->getPrerenderToken()
        );
    }

    public function testCanUncompressGzipResponses()
    {
        $mvcEvent   = new MvcEvent();
        $request    = new HttpRequest();

        $request->setUri('http://www.example.com');
        $request->getHeaders()->addHeaderLine('User-Agent', 'Baiduspider+(+http://www.baidu.com/search/spider.htm)');
        $request->getHeaders()->addHeaderLine('Accept-Encoding', 'gzip');
        $mvcEvent->setRequest($request);

        /** @var \ZfrPrerender\Options\ModuleOptions $moduleOptions */
        $moduleOptions = ServiceManagerFactory::getServiceManager()->get('ZfrPrerender\Options\ModuleOptions');

        $prerenderRequest = new HttpRequest();
        $listener         = new PrerenderListener($moduleOptions);

        $response = $this->getMock('Zend\Http\Response');

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
                   ->will($this->returnValue($response));

        $clientMock->expects($this->once())
                   ->method('getRequest')
                   ->will($this->returnValue($prerenderRequest));

        $headers = new Headers();
        $headers->addHeaderLine('Content-Encoding', 'gzip');

        $response->expects($this->once())
                 ->method('getHeaders')
                 ->will($this->returnValue($headers));

        $response->expects($this->once())
                 ->method('getBody')
                 ->will($this->returnValue(gzencode('original value')));

        $response->expects($this->once())
                 ->method('setContent')
                 ->with('original value');

        $listener->setHttpClient($clientMock);

        $result = $listener->prerenderPage($mvcEvent);

        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertFalse($headers->has('Content-Encoding'), 'Ensure header has been removed');
        $this->assertTrue($headers->has('Content-Length'), 'Ensure content length has been added');
        $this->assertEquals(strlen('original value'), $headers->get('Content-Length')->getFieldValue());
    }
}
