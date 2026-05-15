<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\node\NodeInterface;

/**
 * Validates and normalizes one public job node.
 */
final class JobDetailProvider implements JobDetailProviderInterface {

  /**
   * Constructs a JobDetailProvider object.
   */
  public function __construct(
    private readonly JobNormalizer $jobNormalizer,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function getJob(NodeInterface $node): ?array {
    if ($node->bundle() !== 'job') {
      return NULL;
    }

    if (!$node->isPublished()) {
      return NULL;
    }

    if (!$node->access('view')) {
      return NULL;
    }

    return $this->jobNormalizer->normalize($node);
  }

}
