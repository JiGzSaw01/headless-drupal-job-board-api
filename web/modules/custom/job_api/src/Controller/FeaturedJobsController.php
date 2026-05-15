<?php

declare(strict_types=1);

namespace Drupal\job_api\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\job_api\Service\FeaturedJobsProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns published featured jobs.
 */
final class FeaturedJobsController {

  /**
   * Constructs a FeaturedJobsController object.
   */
  public function __construct(
    private readonly FeaturedJobsProviderInterface $featuredJobsProvider,
  ) {
  }

  /**
   * Lists published featured jobs.
   */
  public function list(Request $request): CacheableJsonResponse {
    $limit = $request->query->getInt('limit', 6);

    $response = new CacheableJsonResponse([
      'data' => $this->featuredJobsProvider->getFeaturedJobs($limit),
    ]);

    $cacheability = (new CacheableMetadata())
      ->setCacheTags(['node_list:job'])
      ->setCacheMaxAge(300);
    $response->addCacheableDependency($cacheability);

    return $response;
  }

}
