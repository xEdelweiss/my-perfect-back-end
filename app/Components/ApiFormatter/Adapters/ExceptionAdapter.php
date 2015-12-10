<?php

namespace App\Components\ApiFormatter\Adapters;

use Illuminate\Contracts\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionAdapter extends AbstractAdapter
{
    /**
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function format($exception)
    {
        // Define the response
        $result = [
            'errors' => trans('messages.sorry'),
        ];

        // Default response of 400
        $statusCode = 400;
        $addDebugData = $this->isDebugEnabled();

        switch (true) {
            case $exception instanceof HttpException:
                $statusCode = $exception->getStatusCode();
                break;
            case $exception instanceof ValidationException:
                $result['errors'] = $exception->errors();
                $addDebugData = false;
                break;
        }

        // Prepare response
        $response = [
            'success' => false,
            'result' => $result,
            'meta' => [
                'version' => config('app.version.api'),
                'debug' => $this->isDebugEnabled(),
            ],
        ];

        // If the app is in debug mode && not Validation exception
        if ($addDebugData)
        {
            $response['debug'] = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        // Return a JSON response with the response array and status code
        return response()->json($response, $statusCode);
    }
}