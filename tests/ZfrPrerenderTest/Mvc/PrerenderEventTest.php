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
use ZfrPrerender\Mvc\PrerenderEvent;

/**
 * @author Michaël Gallego <mic.gallego@gmail.com>
 *
 * @covers \ZfrPrerender\Mvc\PrerenderEvent
 * @group Coverage
 */
class PrerenderEventTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $request  = $this->getMock('Zend\Http\Request');
        $response = $this->getMock('Zend\Http\Response');
        
        $event = new PrerenderEvent();
        $event->setRequest($request);
        $event->setResponse($response);

        $this->assertSame($request, $event->getRequest());
        $this->assertSame($response, $event->getResponse());
    }
}
