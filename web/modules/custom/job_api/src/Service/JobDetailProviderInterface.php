<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\node\NodeInterface;

/**
 * Provides public job detail data for API responses.
 */
interface JobDetailProviderInterface {

  /**
   * Returns normalized public detail data for one job.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node loaded from the route parameter.
   *
   * @return array<string, mixed>|null
   *   Normalized public job data, or NULL when the node is not public.
   */
  public function getJob(NodeInterface $node): ?array;

}
