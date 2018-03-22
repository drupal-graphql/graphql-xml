<?php

namespace Drupal\graphql_xml\Plugin\GraphQL\Fields;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Fields\Routing\Route;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Extract Url objects from xpath values.
 *
 * @GraphQLField(
 *   id = "xml_xpath_url",
 *   name = "xpathToUrl",
 *   secure = true,
 *   type = "Url",
 *   parents = {"XMLElement"},
 *   multi = true,
 *   arguments = {
 *     "query" = "String"
 *   }
 * )
 */
class XPathToUrl extends Route {

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($value instanceof \DOMElement) {
      $xpath = new \DOMXPath($value->ownerDocument);
      foreach ($xpath->query($args['query'], $value) as $el) {
        /** @var $el \DOMElement */
        $iterator = parent::resolveValues(NULL, ['path' => $el->textContent], $context, $info);
        $result = iterator_to_array($iterator);
        reset($result);
        foreach ($result as $row) {
          yield $row;
        }
      }
    }
  }

}
