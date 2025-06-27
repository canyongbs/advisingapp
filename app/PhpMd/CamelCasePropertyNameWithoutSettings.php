<?php

namespace App\PhpMd;

use PHPMD\AbstractNode;
use PHPMD\Rule\Controversial\CamelCasePropertyName;

class CamelCasePropertyNameWithoutSettings extends CamelCasePropertyName
{
    public function apply(AbstractNode $node)
    {
        // @phpstan-ignore method.notFound
        foreach ($node->getParentClasses() as $parentClass) {
            if ($parentClass->getNamespace()->getName() === 'Spatie\LaravelSettings') {
                return; // Skip if the class extends Spatie\LaravelSettings
            }
        }

        $allowUnderscore = $this->getBooleanProperty('allow-underscore');

        $pattern = '/^\$[a-z][a-zA-Z0-9]*$/';

        if ($allowUnderscore === true) {
            $pattern = '/^\$[_]?[a-z][a-zA-Z0-9]*$/';
        }

        // @phpstan-ignore method.notFound
        foreach ($node->getProperties() as $property) {
            $propertyName = $property->getName();

            if (! preg_match($pattern, $propertyName)) {
                $this->addViolation(
                    $node,
                    [
                        $propertyName,
                    ]
                );
            }
        }
    }
}
