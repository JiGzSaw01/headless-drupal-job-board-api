<?php

declare(strict_types=1);

namespace Drupal\job_api\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\job_api\Service\JobListingProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns public job listings.
 */
final class JobListingController {

  /**
   * Constructs a JobListingController object.
   */
  public function __construct(
    private readonly JobListingProviderInterface $jobListingProvider,
  ) {
  }

  /**
   * Lists published jobs.
   */
  public function list(Request $request): CacheableJsonResponse {
    $filters = [
      'location' => $request->query->get('location'),
      'job_type' => $request->query->get('job_type'),
    ];
    $page = $request->query->getInt('page', 1);
    $limit = $request->query->getInt('limit', 10);

    $response = new CacheableJsonResponse(
      $this->jobListingProvider->getJobs($filters, $page, $limit)
    );

    $cacheability = (new CacheableMetadata())
      ->setCacheTags(['node_list:job'])
      ->setCacheMaxAge(300);
    $response->addCacheableDependency($cacheability);

    return $response;
  }

}
