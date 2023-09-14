<?php

namespace Assist\IntegrationAi\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

// TODO This interface will be applied to models that need to potentially provide more context to the AI implementation
// An example of this is when the AI is operating in the context of a particular record, such as a student that we want
// The AI to be aware of in order to provide the best feedback possible
/** @mixin Model */
interface ProvidesDynamicContext
{
    public function getDynamicContext(): string;
}
