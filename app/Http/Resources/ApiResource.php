<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
  protected bool $success;
  protected string $message;
  protected int $code;

  public function __construct($resource = null, bool $success = true, string $message = 'OK', int $code = 200)
  {
    parent::__construct($resource);
    $this->success = $success;
    $this->message = $message;
    $this->code = $code;
  }

  public function toArray($request): array
  {
    return [
      'success' => $this->success,
      'code' => $this->code,
      'message' => $this->message,
      'data' => $this->resource,
    ];
  }

  public function toResponse($request)
  {
    return response()->json($this->toArray($request), $this->code);
  }

  public static function error(string $message = 'Error', int $code = 400, $errors = null): self
  {
    $data = $errors ?? null;
    return new self($data, false, $message, $code);
  }

  public static function success($data = null, string $message = 'OK', int $code = 200): self
  {
    return new self($data, true, $message, $code);
  }
}
