<?php

namespace Phalcon\Http\Response;

/**
 * Phalcon\Http\Response\HeadersInterface
 *
 * Interface for Phalcon\Http\Response\Headers compatible bags
 */
interface HeadersInterface
{

    /**
     * Sets a header to be sent at the end of the request
     *
     * @param string $name
     * @param string $value
     */
    public function set($name, $value);

    /**
     * Gets a header value from the internal bag
     *
     * @param string $name
     * @return string
     */
    public function get($name);

    /**
     * Sets a raw header to be sent at the end of the request
     *
     * @param string $header
     */
    public function setRaw($header);

    /**
     * Sends the headers to the client
     *
     * @return boolean
     */
    public function send();

    /**
     * Removes a header to be sent at the end of the request
     */
    public function remove($header);

    /**
     * Reset set headers
     *
     */
    public function reset();

    /**
     * Restore a \Phalcon\Http\Response\Headers object
     *
     * @param array $data
     * @return \Phalcon\Http\Response\HeadersInterface
     */
    public static function __set_state($data);
}
