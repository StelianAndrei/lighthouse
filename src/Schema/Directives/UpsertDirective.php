<?php

namespace Nuwave\Lighthouse\Schema\Directives;

use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use Nuwave\Lighthouse\Execution\Arguments\ResolveNested;
use Nuwave\Lighthouse\Execution\Arguments\SaveModel;
use Nuwave\Lighthouse\Execution\Arguments\UpsertModel;
use Nuwave\Lighthouse\Support\Utils;

class UpsertDirective extends MutationExecutorDirective
{
    public function name(): string
    {
        return 'upsert';
    }

    public static function definition(): string
    {
        return /* @lang GraphQL */ <<<'SDL'
"""
Create or update an Eloquent model with the input values of the field.
"""
directive @upsert(
  """
  Specify the class name of the model to use.
  This is only needed when the default model resolution does not work.
  """
  model: String

  """
  Set to `true` to use global ids for finding the model.
  If set to `false`, regular non-global ids are used.
  """
  globalId: Boolean = false
) on FIELD_DEFINITION
SDL;
    }

    /**
     * Execute a mutation on a model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *         An empty instance of the model that should be mutated.
     * @param  \Nuwave\Lighthouse\Execution\Arguments\ArgumentSet|\Nuwave\Lighthouse\Execution\Arguments\ArgumentSet[]  $args
     *         The user given input arguments for mutating this model.
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]
     */
    public function __invoke($model, $args)
    {
        $relation = null;
        if ($relationName = $this->directiveArgValue('relation')) {
            /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
            $relation = $model->{$relationName}();
            $model = $relation->make();
        }

        $upsert = new ResolveNested(new UpsertModel(new SaveModel($relation)));

        return Utils::applyEach(
            static function (ArgumentSet $argumentSet) use ($upsert, $model) {
                return $upsert($model, $argumentSet);
            },
            $args
        );
    }
}
