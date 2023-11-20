<?php

namespace Assist\Assistant\Services\AIInterface\Enums;

enum AIChatMessageFrom: string
{
    case User = 'user';

    case Assistant = 'assistant';

    case System = 'system';
}
