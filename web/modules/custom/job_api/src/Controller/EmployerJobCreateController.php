<?php

declare(strict_types=1);

namespace Drupal\job_api\Controller;

use Drupal\job_api\Service\EmployerJobCreatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Creates employer-owned job posts.
 */
final class EmployerJobCreateController {

  /**
   * Constructs an EmployerJobCreateController object.
   */
  public function __construct(
    private readonly EmployerJobCreatorInterface $jobCreator,
  ) {
  }

  /**
   * Creates a job from a JSON request body.
   */
  public function create(Request $request): JsonResponse {
    $content = json_decode($request->getContent(), TRUE) ?? [];

    if (json_last_error() !== JSON_ERROR_NONE) {
      return new JsonResponse([
        'error' => 'Invalid JSON payload.',
      ], JsonResponse::HTTP_BAD_REQUEST);
    }

    if (!is_array($content)) {
      return new JsonResponse([
        'error' => 'JSON payload must be an object.',
      ], JsonResponse::HTTP_BAD_REQUEST);
    }

    try {
      $job = $this->jobCreator->create($content);
    }
    catch (HttpExceptionInterface $exception) {
      return new JsonResponse([
        'error' => $exception->getMessage(),
      ], $exception->getStatusCode());
    }

    return new JsonResponse([
      'data' => $job,
    ], JsonResponse::HTTP_CREATED);
  }

}
