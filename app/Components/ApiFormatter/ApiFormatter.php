<?php

namespace App\Components\ApiFormatter;

use App\Components\ApiFormatter\Adapters\AbstractAdapter;
use App\Components\ApiFormatter\Adapters\ExceptionAdapter;
use App\Components\ApiFormatter\Adapters\ResponseAdapter;

class ApiFormatter
{

    /**
     * @param \Illuminate\Http\Response $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatResponse($response)
    {
        return $this->format($response, new ResponseAdapter());
    }

    /**
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatException($exception)
    {
        return $this->format($exception, new ExceptionAdapter());
    }

    /**
     * @param $data
     * @param AbstractAdapter $adapter
     * @return \Illuminate\Http\JsonResponse
     */
    protected function format($data, $adapter)
    {
        return $adapter->format($data);
    }
}