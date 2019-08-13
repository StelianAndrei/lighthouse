<?php

namespace Nuwave\Lighthouse\Schema\Factories;

use GraphQL\Utils\AST;
use GraphQL\Type\Definition\EnumType;
use Nuwave\Lighthouse\Execution\Arguments\ResolveNestedBefore;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\Extensions\ArgumentExtensions;
use Nuwave\Lighthouse\Schema\Values\ArgumentValue;

class ArgumentFactory
{
    /**
     * @var \Nuwave\Lighthouse\Schema\Factories\DirectiveFactory
     */
    protected $directiveFactory;

    /**
     * ArgumentFactory constructor.
     * @param \Nuwave\Lighthouse\Schema\Factories\DirectiveFactory $directiveFactory
     */
    public function __construct(DirectiveFactory $directiveFactory)
    {
        $this->directiveFactory = $directiveFactory;
    }

    /**
     * Convert argument definition to type.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\ArgumentValue  $argumentValue
     * @return array
     */
    public function handle(ArgumentValue $argumentValue): array
    {
        $definition = $argumentValue->getAstNode();

        $argumentType = $argumentValue->getType();

        $fieldArgument = [
            'name' => $argumentValue->getName(),
            'description' => data_get($definition->description, 'value'),
            'type' => $argumentType,
            'astNode' => $definition,
        ];

        if ($defaultValue = $definition->defaultValue) {
            $fieldArgument += [
                // webonyx/graphql-php expects the internal value here, whereas the
                // SDL uses the ENUM's name, so we run the conversion here
                'defaultValue' => $argumentType instanceof EnumType
                    ? $argumentType->getValue($defaultValue->value)->value
                    : AST::valueFromASTUntyped($defaultValue),
            ];
        }

        $extensions = new ArgumentExtensions();
        $extensions->resolveBefore = $this->directiveFactory->createAssociatedDirectivesOfType($definition, ResolveNestedBefore::class);

        // Add any dynamically declared public properties of the FieldArgument
        $fieldArgument += get_object_vars($argumentValue);

        // Used to construct a FieldArgument class
        return $fieldArgument;
    }
}
