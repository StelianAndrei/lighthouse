<?php

namespace Nuwave\Lighthouse\Schema\Directives;

use Nuwave\Lighthouse\Support\Contracts\FieldResolver;
use Nuwave\Lighthouse\Support\Contracts\DefinedDirective;

class BelongsToDirective extends RelationDirective implements FieldResolver, DefinedDirective
{
    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'belongsTo';
    }

    public static function definition(): string
    {
        return /* @lang GraphQL */ <<<'SDL'
"""
Resolves a field through the Eloquent `BelongsTo` relationship.
"""
directive @belongsTo(  
  """
  Specify the relationship method name in the model class,
  if it is named different from the field in the schema.
  """
  relation: String

  """
  Apply scopes to the underlying query.
  """
  scopes: [String!]
) on FIELD_DEFINITION
SDL;
    }
}
