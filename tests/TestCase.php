<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://mb.dev7';

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var Faker\Generator $faker */
    protected $faker;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $this->client = new GuzzleHttp\Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
            'cookies' => true,
        ]);

        $this->faker = Faker\Factory::create();
    }

    /**
     * @param int $userId
     * @return $this
     */
    protected function login($userId = 1)
    {
        $this->client->get("/dev/fakelogin/{$userId}");
        return $this;
    }

    /**
     * @return $this
     */
    protected function logout()
    {
        $this->client->get('/auth/logout');
        return $this;
    }

    /**
     * @param $method
     * @param null $uri
     * @param array $options
     * @return mixed
     */
    protected function request($method, $uri = null, array $options = [])
    {
        $response = $this->client->request($method, $uri, $options);

        return new class($response) {

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
                PHPUnit_Framework_Assert::assertEquals($expected, $actual, $message ?: "Key [{$key}]=[{$actual}] is not equal to expected [{$expected}]");

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
        };
    }
}
