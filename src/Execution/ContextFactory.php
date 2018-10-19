<?php

namespace Nuwave\Lighthouse\Execution;

use Illuminate\Http\Request;
use Nuwave\Lighthouse\Schema\Context;
use Nuwave\Lighthouse\Support\Contracts\CreatesContext;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ContextFactory implements CreatesContext
{
    /**
     * Generate GraphQL context.
     *
     * @param Request $request
     *
     * @return GraphQLContext
     */
    public function generate(Request $request): GraphQLContext
    {
        return new Context($request, $request->user());
    }
}