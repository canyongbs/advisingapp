# Custom Metadata

The Advising App platform provides many opportunities for system administrators to create custom metadata that is then relatable to other entities. Generally, this custom metadata is used to represent some sort of "state" or "status" for an entity. A good example of this can be understood with `Case` and `CaseStatus`.

The `Case` model belongs to the `CaseStatus` model, which is configurable by system administrators with appropriate permissions. This means that any number of `CaseStatus` records can be created, to serve all of the needs of the organization using the application.

But, within the Advising App application, the system occasionally needs to know what this metadata actually means, and what a specific `CaseStatus` actually represents. In order to facilitate this, we've added a `System{Entity}Classification` enum in places where the system does need to understand this data in some capacity.

For the Case example, that enum looks like this:

```php
enum SystemCaseClassification: string
{
    case Open = 'open';

    case InProgress = 'in_progress';

    case Closed = 'closed';

    case Custom = 'custom';
}
```

When defining a custom piece of metadata, system administrators must associate their `CaseStatus` with a classification, so that the system can carry out associated application logic against these records.

This pattern still allows for the flexibility of custom metadata, while providing a way to hook into application logic that is dependent on rigid definitions.
