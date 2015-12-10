<?php

namespace App\Components\ApiFormatter\Adapters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ResponseAdapter extends AbstractAdapter
{
    /**
     * @param \Illuminate\Http\Response $response
     * @return JsonResponse
     * @throws \Exception
     */
    public function format($response)
    {
        switch (true) {
            case $response instanceof Response:
                $result = $this->getResultFromResponse($response);
                break;
            case $response instanceof JsonResponse:
                /** @var JsonResponse $response */
                $result = [
                    'type' => 'mixed',
                    'data' => $response->getData()
                ];
                break;
            case $response instanceof RedirectResponse:
                /** @var RedirectResponse $response */
                $result = [
                    'type' => 'redirect',
                    'data' => $response->getTargetUrl(),
                ];
                break;
            default:
                throw new \Exception('Object of class ' . get_class($response) . ' could not be converted to JSON');
        }

        // keep original response headers
        $statusCode = $response->getStatusCode();
        $headers = array_except($response->headers->all(), 'content-type');

        $resultResponse = response()->json([
            'success' => true,
            'result' => $result,
            'meta' => [
                'version' => config('app.version.api'),
                'debug' => $this->isDebugEnabled(),
            ],
        ], $statusCode, $headers);

        return $resultResponse;
    }

    /**
     * @param Response $response
     * @return mixed
     * @throws \Exception
     */
    protected function getResultFromResponse(Response $response)
    {
        $result = $response->getOriginalContent();

        switch (true) {
            case is_string($result):
                /** @var string $result */
                return [
                    'type' => 'string',
                    'data' => $result,
                ];
            case $result instanceof View:
                /** @var View $result */
                return [
                    'type' => 'view',
                    'data' => $result->render(),
                ];
            case $result instanceof Model:
                /** @var Model $result */
                return [
                    'type' => strtolower((new \ReflectionClass($result))->getShortName()),
                    'data' => $result->toArray(),
                ];
            case $result instanceof Collection:
            case is_array($result):
                return [
                    'type' => 'collection',
                    'data' => $result,
                ];
            default:
                throw new \Exception('Object of class ' . get_class($response) . ' could not be converted to JSON');
        }
    }
}