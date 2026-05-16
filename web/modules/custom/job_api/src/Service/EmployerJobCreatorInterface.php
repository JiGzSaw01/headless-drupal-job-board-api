<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

/**
 * Creates employer-owned job posts.
 */
interface EmployerJobCreatorInterface {

  /**
   * Creates a job node from validated API input.
   *
   * @param array<string, mixed> $data
   *   Decoded JSON request data.
   *
   * @return array<string, mixed>
   *   Created job response data.
   */
  public function create(array $data): array;

}
