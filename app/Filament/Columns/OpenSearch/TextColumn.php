<?php

namespace App\Filament\Columns\OpenSearch;

use App\Filament\Columns\OpenSearch\Concerns\OpenSearchQueryDefault;

class TextColumn extends \Filament\Tables\Columns\TextColumn implements OpenSearchColumn
{
    use OpenSearchQueryDefault;
}
