<?php

namespace Drupal\graphql_xml\Plugin\GraphQL\Fields;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Get an xml elements inner content string.
 *
 * @GraphQLField(
 *   id = "xml_content",
 *   secure = true,
 *   type = "String",
 *   name = "content",
 *   parents = { "XMLElement" }
 * )
 */
class XMLContent extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($value instanceof \DOMElement) {
      yield implode('', array_map(function ($child) {
        if ($child instanceof \DOMText) {
          return $child->nodeValue;
        }
        elseif ($child instanceof \DOMElement) {
          return $child->ownerDocument->saveXML($child);
        }
        else {
          return '';
        }
      }, iterator_to_array($value->childNodes)));
    }
  }

}
