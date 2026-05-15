<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

/**
 * Provides public job listings for API responses.
 */
interface JobListingProviderInterface {

  /**
   * Returns a paginated public job listing.
   *
   * @param array<string, string|null> $filters
   *   Supported listing filters.
   * @param int $page
   *   Page number starting at 1.
   * @param int $limit
   *   Number of jobs per page.
   *
   * @return array{data: array<int, array<string, mixed>>, meta: array<string, int|string|null>}
   *   Listing data and pagination metadata.
   */
  public function getJobs(array $filters = [], int $page = 1, int $limit = 10): array;

}
