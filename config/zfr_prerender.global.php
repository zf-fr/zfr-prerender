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

return array(
    'zfr_prerender' => array(
        /**
         * Prerender URL to use
         *
         * This module is already preconfigured to work with a hosted Prerender service, but you
         * should host your own for security
         */
        // 'prerender_url' => '',

        /**
         * BOT crawler agents. You can add any crawlers you want
         *
         * This module is already configured with some crawlers (check the module.config.php file)
         */
        // 'crawler_user_agents' => array(),

        /**
         * Extensions to ignore (meaning that if we ask for a resource, we will not
         * request the Prerender service)
         *
         * This module is already configured with some ignored extensions (check the module.config.php file)
         */
        // 'ignored_extensions' => array(),

        /**
         * URLs to whitelist. Because this module happen very early in the dispatching process (before
         * routing happen), remember to specify URL, NOT ROUTES!
         *
         * If at least one value is provided in the whitelist array, then only those URL will be prerendered. It
         * uses a regex, so you can be specific here!
         */
        // 'whitelist_urls' => array(),

        /**
         * URLs to blacklist. Because this module happen very early in the dispatching process (before
         * routing happen), remember to specify URL, NOT ROUTES!
         *
         * If at least one value is provided in the blacklist array, then all URL will be prerendered, excepting
         * those ones. It uses a regex, so you can be specific here!
         */
        // 'blacklist_urls' => array()
    )
);
