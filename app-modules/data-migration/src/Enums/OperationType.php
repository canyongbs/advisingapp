<?php

namespace AdvisingApp\DataMigration\Enums;

enum OperationType: string
{
    case Tenant = 'tenant';

    case Landlord = 'landlord';
}
