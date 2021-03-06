<?php

namespace Fut\Connector;

/**
 * Interface EndpointInterface
 */
interface EndpointInterface
{
	/**
     * connects to the endpoint
     */
    public function connect($backupcode);

    /**
     * exports needed data to reconnect again with actually login
     *
     * @return string[]
     */
    public function exportLoginData();
}