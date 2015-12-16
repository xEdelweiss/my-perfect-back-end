<?php

namespace Tests\Utils;

use PHPUnit_Framework_Assert;

class Response {

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $result;

    /**
     *  constructor.
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct(\GuzzleHttp\Psr7\Response $response)
    {
        $this->response = $response;
        $this->result = json_decode($this->getContents(), true);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->response->getBody()->getContents();
    }

    public function dump()
    {
        dd($this->result);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->result, JSON_PRETTY_PRINT);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return array_get($this->result, $key);
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getRawResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return array_get($this->result, 'result.data.id');
    }

    /**
     * @return null|string
     */
    public function getErrors()
    {
        return array_get($this->result, 'result.errors');
    }

    /**
     * @param $key
     * @param string $message
     * @return $this
     */
    public function assertKeyExists($key, $message = null)
    {
        PHPUnit_Framework_Assert::assertTrue(array_has($this->result, $key), $message ?: "Key [{$key}] does not exist");

        return $this;
    }

    /**
     * @param $key
     * @param string $message
     * @return $this
     */
    public function assertKeyNotExists($key, $message = null)
    {
        PHPUnit_Framework_Assert::assertFalse(array_has($this->result, $key), $message ?: "Key [{$key}] does exist");

        return $this;
    }

    /**
     * @param $key
     * @param $expected
     * @param string $message
     * @return $this
     */
    public function assertKeyEquals($key, $expected, $message = null)
    {
        $actual = array_get($this->result, $key);

        $actualEncoded = json_encode($actual);
        $expectedEncoded = json_encode($expected);

        PHPUnit_Framework_Assert::assertEquals($expected, $actual, $message ?: "Key [{$key}]={$actualEncoded} is not equal to expected {$expectedEncoded}");

        return $this;
    }

    /**
     * @param array $keys
     * @param array $skipKeys
     * @return $this
     */
    public function assertKeysExist($keys, $skipKeys = [])
    {
        foreach ($keys as $key) {
            if (in_array($key, $skipKeys)) {
                continue;
            }

            $this->assertKeyExists($key);
        }

        return $this;
    }

    /**
     * @param array $keys
     * @param array $skipKeys
     * @return $this
     */
    public function assertKeysNotExist($keys, $skipKeys = [])
    {
        foreach ($keys as $key) {
            if (in_array($key, $skipKeys)) {
                continue;
            }

            $this->assertKeyNotExists($key);
        }

        return $this;
    }

    /**
     * @param array $keysExpectedValuesMap
     * @param array $skipKeys
     * @return $this
     */
    public function assertKeysEqual($keysExpectedValuesMap, $skipKeys = [])
    {
        foreach ($keysExpectedValuesMap as $key => $expected) {
            if (in_array($key, $skipKeys)) {
                continue;
            }

            $this->assertKeyEquals($key, $expected);
        }

        return $this;
    }

    /**
     * @param array $keysExpectedValuesMap
     * @param array $skipKeys
     * @return $this
     */
    public function assertDataKeysEqual($keysExpectedValuesMap, $skipKeys = [])
    {
        foreach ($keysExpectedValuesMap as $key => $expected) {
            if (in_array($key, $skipKeys)) {
                continue;
            }

            $this->assertKeyEquals("result.data.{$key}", $expected);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $expected
     * @param null $message
     * @return $this
     */
    public function assertKeyChildrenCountEquals($key, $expected, $message = null)
    {
        $actual = count(array_get($this->result, $key, []));
        PHPUnit_Framework_Assert::assertEquals($expected, $actual, $message ?: "Failed asserting that [{$key}] contains expected [{$expected}] elements ([{$actual}] found)");

        return $this;
    }

    /**
     * @param $expected
     * @param string $message
     * @return $this
     */
    public function assertStatus($expected, $message = null)
    {
        $actual = $this->response->getStatusCode();
        $message = $message ?: "Failed asserting that [{$actual}] is [{$expected}]";

        if ($this->getErrors()) {
            $message .= '. ' . json_encode($this->getErrors(), JSON_PRETTY_PRINT);
        }

        PHPUnit_Framework_Assert::assertEquals($expected, $actual, $message);

        return $this;
    }
}