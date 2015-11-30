<?php

namespace Fut;

/**
 * connector interface
 *
 * interface ConnectorInterface
 * @package Fut
 */
interface ConnectorInterface
{

    /**
     * connect with the appropriate connector
     *
     * @return $this
     */
    public function connect($backupcode);

    /**
     * returns needed data for login again
     *
     * @return string[]
     */
    public function export();
}