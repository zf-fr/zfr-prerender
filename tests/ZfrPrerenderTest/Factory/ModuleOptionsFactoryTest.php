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

namespace ZfrPrerenderTest\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use ZfrPrerenderTest\Util\ServiceManagerFactory;

/**
 * @author Michaël Gallego <mic.gallego@gmail.com>
 *
 * @covers \ZfrPrerender\Factory\ModuleOptionsFactory
 * @group Coverage
 */
class ModuleOptionsFactoryTest extends TestCase
{
    public function testCanCreateOptions()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $options        = $serviceManager->get('ZfrPrerender\Options\ModuleOptions');

        $this->assertInstanceOf('ZfrPrerender\Options\ModuleOptions', $options);
        $this->assertEquals('http://prerender.herokuapp.com', $options->getPrerenderUrl());
        $this->assertEquals(array('googlebot', 'yahoo', 'bingbot', 'baiduspider', 'facebookexternalhit', 'twitterbot'), $options->getCrawlerUserAgents());
        $this->assertEmpty($options->getWhitelistUrls());
        $this->assertEmpty($options->getBlacklistUrls());
    }
}
