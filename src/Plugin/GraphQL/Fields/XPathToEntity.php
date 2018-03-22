<?php

namespace Drupal\graphql_xml\Plugin\GraphQL\Fields;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extract entities from xpath values.
 *
 * @GraphQLField(
 *   id = "xml_xpath_entity",
 *   name = "xpathToEntity",
 *   secure = true,
 *   type = "Entity",
 *   parents = {"XMLElement"},
 *   multi = true,
 *   arguments = {
 *     "type" = "String",
 *     "query" = "String"
 *   }
 * )
 */
class XPathToEntity extends XMLXPath implements ContainerFactoryPluginInterface {
  use DependencySerializationTrait;

  /**
   * The entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $pluginId,
    $pluginDefinition
  ) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityRepositoryInterface $entityRepository
  ) {
    $this->entityRepository = $entityRepository;
    parent::__construct($configuration, $pluginId, $pluginDefinition);
  }

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    foreach (parent::resolveValues($value, $args, $context, $info) as $item) {
      /** @var \DOMElement $item */
      if ($entity = $this->entityRepository->loadEntityByUuid($args['type'], $item->textContent)) {
        if ($entity->access('view')) {
          yield $entity;
        }
      }
    }
  }

}
