<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

/**
 * list employer-owned job posts.
 */
interface EmployerJobDashboardProviderInterface {

  /**
   * Lists employer-owned job posts.
   *
   * @param int $page
   *   The page number to retrieve.
   * @param int $limit
   *   The number of jobs to retrieve per page.
   *
   * @return array<string, mixed>
   *   Lists employer-owned job posts.
   */
  public function getJobs(int $page = 1, int $limit = 10): array;

}
