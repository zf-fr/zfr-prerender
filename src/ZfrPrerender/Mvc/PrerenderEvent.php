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

namespace ZfrPrerender\Mvc;

use Zend\EventManager\Event;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

/**
 * Event that is thrown before and after a Prerender.io request
 *
 * @author Michaël Gallego
 * @licence MIT
 */
class PrerenderEvent extends Event
{
    /**
     * Event constants
     */
    const EVENT_PRERENDER_PRE  = 'prerender.pre';
    const EVENT_PRERENDER_POST = 'prerender.post';

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var HttpResponse|null
     */
    protected $response;

    /**
     * @param HttpRequest  $request
     * @param HttpResponse $response
     */
    public function __construct(HttpRequest $request, HttpResponse $response = null)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Get the request
     *
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response
     *
     * @return HttpResponse|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
