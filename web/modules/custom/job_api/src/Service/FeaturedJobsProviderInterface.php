<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

/**
 * Provides featured jobs for public API responses.
 */
interface FeaturedJobsProviderInterface {

  /**
   * Returns published featured jobs.
   *
   * @param int $limit
   *   Maximum number of jobs to return.
   *
   * @return array<int, array<string, mixed>>
   *   Normalized public job data.
   */
  public function getFeaturedJobs(int $limit = 6): array;

}
