<?php

namespace Assist\Audit\Models\Concerns;

use OwenIt\Auditing\Auditable as OwenItAuditable;

trait Auditable
{
    use OwenItAuditable;
    use AuditableManyToMany;
}
