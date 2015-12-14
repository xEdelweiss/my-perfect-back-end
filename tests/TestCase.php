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
     * @return \Tests\Utils\Response
     */
    protected function request($method, $uri = null, array $options = [])
    {
        $response = $this->client->request($method, $uri, $options);

        return new \Tests\Utils\Response($response);
    }
}
