<?php

namespace Lokal\Butler\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;

class ResponseResources extends JsonResource
{
    protected bool $isPaginated;

    protected int $status = 200;

    protected string $message = '';

    protected ?\Closure $transformer = null;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        parent::__construct($resource);

        $this->isPaginated = ($resource instanceof AbstractPaginator || $resource instanceof AbstractCursorPaginator);
    }

    /**
     * @return $this
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function useTransformer(\Closure $callable): static
    {
        $this->transformer = $callable;

        return $this;
    }

    /**
     * Get API Response.
     */
    public function apiResponse(): array
    {
        return $this->getResponseMessage(
            $this->transformData($this->resource)
        );
    }

    /**
     * Get API Paginate Response.
     */
    public function apiPaginateResponse(): array
    {
        return $this->getPaginateResponseMessage(
            $this->transformData(
                $this->resource->getCollection()
            )
        );
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->apiResponse();
    }

    /**
     * @response Single response data
     */
    protected function getResponseMessage(mixed $data): array
    {
        return [
            /**
             * The response status.
             *
             * @var int $status
             */
            'status' => http_response_code($this->status),
            /**
             * The response message
             *
             * @var string $message .
             */
            'message' => $this->message,
            /**
             * The response data
             *
             * @var array $data .
             */
            'data' => $data,
        ];
    }

    /**
     * @response Paginated response data
     */
    protected function getPaginateResponseMessage(mixed $data): array
    {
        return [
            /**
             * The response status.
             *
             * @var bool $success
             */
            'status' => $this->status,

            /**
             * The response message
             *
             * @var string $message .
             */
            'message' => $this->message,

            /**
             * The response data
             *
             * @var array $data .
             */
            'data' => $data,

            /**
             * The response pagination
             *
             * @var array $pagination .
             */
            'pagination' => collect($this->resource)->except('data'),
        ];
    }

    protected function transformData(mixed $data): mixed
    {
        if ($this->transformer instanceof \Closure) {
            $data = call_user_func($this->transformer, $data);
        }

        if (is_null($this->message)) {
            $this->message = __('response.action.success');
        }

        return $data;
    }
}