<?php
use Lokal\Butler\Resources\ResponseResources;

if (! function_exists('api_response')) {
    function api_response($resource, callable $transformer = null, string $message = 'success'): array
    {
        $response = (new ResponseResources($resource));

        if (is_callable($transformer)) {
            $response->useTransformer($transformer);
        }

        return $response->setMessage($message)->apiResponse();
    }
}

if (! function_exists('paginate_response')) {
    function paginate_response($resource, callable $transformer = null, string $message = 'success'): array
    {
        $response = (new ResponseResources($resource));

        if (is_callable($transformer)) {
            $response->useTransformer($transformer);
        }

        return $response->setMessage($message)->apiPaginateResponse();
    }
}