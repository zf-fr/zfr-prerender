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

namespace ZfrPrerenderTest\Options;

use PHPUnit_Framework_TestCase as TestCase;
use ZfrPrerender\Options\ModuleOptions;

/**
 * @author Michaël Gallego <mic.gallego@gmail.com>
 *
 * @covers \ZfrPrerender\Options\ModuleOptions
 * @group Coverage
 */
class ModuleOptionsTest extends TestCase
{
    public function testSettersGetters()
    {
        $moduleOptions = new ModuleOptions();

        $moduleOptions->setCrawlerUserAgents(array('baidu'));
        $this->assertEquals(array('baidu'), $moduleOptions->getCrawlerUserAgents());

        $moduleOptions->setIgnoredExtensions(array('.jpg'));
        $this->assertEquals(array('.jpg'), $moduleOptions->getIgnoredExtensions());

        $moduleOptions->setPrerenderUrl('http://myprerender.com');
        $this->assertEquals('http://myprerender.com', $moduleOptions->getPrerenderUrl());

        $moduleOptions->setWhitelistUrls(array('/users/*'));
        $this->assertEquals(array('/users/*'), $moduleOptions->getWhitelistUrls());

        $moduleOptions->setBlacklistUrls(array('/users/*'));
        $this->assertEquals(array('/users/*'), $moduleOptions->getBlacklistUrls());
    }
}
