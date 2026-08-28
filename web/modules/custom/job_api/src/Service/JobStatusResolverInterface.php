<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\node\NodeInterface;

/**
 * Interface for resolving job statuses.
 */
interface JobStatusResolverInterface {

  /**
   * Resolves the status of a job based on its current state.
   *
   * @param \Drupal\node\NodeInterface $job
   *   The job data.
   *
   * @return string
   *   The resolved job status.
   */
  public function resolve(NodeInterface $job): string;

}
