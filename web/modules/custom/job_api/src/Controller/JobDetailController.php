<?php

declare(strict_types=1);

namespace Drupal\job_api\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\job_api\Service\JobDetailProviderInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns public job detail responses.
 */
final class JobDetailController {

  /**
   * Constructs a JobDetailController object.
   */
  public function __construct(
    private readonly JobDetailProviderInterface $jobDetailProvider,
  ) {
  }

  /**
   * Shows one published job.
   */
  public function view(NodeInterface $node): CacheableJsonResponse {
    $job = $this->jobDetailProvider->getJob($node);

    if ($job === NULL) {
      throw new NotFoundHttpException();
    }

    $response = new CacheableJsonResponse([
      'data' => $job,
    ]);

    $cacheability = (new CacheableMetadata())
      ->setCacheTags($node->getCacheTags())
      ->setCacheContexts($node->getCacheContexts())
      ->setCacheMaxAge(300);
    $response->addCacheableDependency($cacheability);

    return $response;
  }

}
