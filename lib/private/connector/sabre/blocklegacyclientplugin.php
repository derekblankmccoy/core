<?php
/**
 * @author Lukas Reschke <lukas@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Connector\Sabre;
use Sabre\HTTP\RequestInterface;

/**
 * Class BlockLegacyClientPlugin is used to detect old legacy ownCloud desktop
 * sync clients and returns a 503 status to those clients.
 *
 * @package OC\Connector\Sabre
 */
class BlockLegacyClientPlugin extends \Sabre\DAV\ServerPlugin {
	/** @var \Sabre\DAV\Server */
	protected $server;

	/**
	 * @param \Sabre\DAV\Server $server
	 * @return void
	 */
	function initialize(\Sabre\DAV\Server  $server) {
		$this->server = $server;
		$this->server->on('beforeMethod', [$this, 'beforeHandler'], 200);
	}

	/**
	 * Detects all mirall versions below 1.6.0 and returns a 504
	 * @param RequestInterface $request
	 * @return bool
	 */
	function beforeHandler(RequestInterface $request) {
		$userAgent = $request->getRawServerValue('HTTP_USER_AGENT');
		preg_match("/(?:mirall\\/)([\d.]+)/i", $userAgent, $versionMatches);
		if(isset($versionMatches[1]) && version_compare($versionMatches[1], '1.6.0') === -1) {
			// FIXME: Why can't we uset he ResponseInterface here?
			http_response_code(503);
			exit();
		}
	}
}
