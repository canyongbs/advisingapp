<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

declare(strict_types = 1);

namespace App\Rector;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddInteractsWithMediaUseTagRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add @use InteractsWithMedia<\App\Models\Media> annotation to classes using InteractsWithMedia trait',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        use Spatie\MediaLibrary\InteractsWithMedia;

                        class MyModel extends Model
                        {
                            use InteractsWithMedia;
                        }
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        use App\Models\Media;
                        use Spatie\MediaLibrary\InteractsWithMedia;

                        class MyModel extends Model
                        {
                            /** @use InteractsWithMedia<Media> */
                            use InteractsWithMedia;
                        }
                        CODE_SAMPLE,
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Namespace_::class];
    }

    public function refactor(Node $node): ?Node
    {
        /** @var Namespace_ $node */
        $class = $this->findClass($node);

        if ($class === null) {
            return null;
        }

        $traitUse = $this->findInteractsWithMediaTraitUse($class);

        if ($traitUse === null) {
            return null;
        }

        if ($this->hasUseTag($traitUse)) {
            return null;
        }

        if ($this->hasConflictingMediaImport($node)) {
            $traitUse->setDocComment(new Doc('/** @use InteractsWithMedia<\App\Models\Media> */'));
        } else {
            $traitUse->setDocComment(new Doc('/** @use InteractsWithMedia<Media> */'));

            if (! $this->hasUseImport($node, 'App\Models\Media')) {
                $this->addUseImport($node, 'App\Models\Media');
            }
        }

        return $node;
    }

    private function findClass(Namespace_ $namespace): ?Class_
    {
        foreach ($namespace->stmts as $stmt) {
            if ($stmt instanceof Class_) {
                return $stmt;
            }
        }

        return null;
    }

    private function findInteractsWithMediaTraitUse(Class_ $class): ?TraitUse
    {
        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof TraitUse && $this->hasInteractsWithMediaTrait($stmt)) {
                return $stmt;
            }
        }

        return null;
    }

    private function hasInteractsWithMediaTrait(TraitUse $traitUse): bool
    {
        foreach ($traitUse->traits as $trait) {
            if ($this->isName($trait, 'Spatie\MediaLibrary\InteractsWithMedia')) {
                return true;
            }
        }

        return false;
    }

    private function hasUseTag(TraitUse $traitUse): bool
    {
        $docComment = $traitUse->getDocComment();

        if ($docComment === null) {
            return false;
        }

        return str_contains($docComment->getText(), '@use InteractsWithMedia<');
    }

    private function hasUseImport(Namespace_ $namespace, string $fullyQualifiedName): bool
    {
        foreach ($namespace->stmts as $stmt) {
            if (! $stmt instanceof Use_) {
                continue;
            }

            foreach ($stmt->uses as $use) {
                if ($use->name->toString() === $fullyQualifiedName) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasConflictingMediaImport(Namespace_ $namespace): bool
    {
        foreach ($namespace->stmts as $stmt) {
            if (! $stmt instanceof Use_) {
                continue;
            }

            foreach ($stmt->uses as $use) {
                $shortName = $use->alias?->toString() ?? $use->name->getLast();

                if ($shortName === 'Media' && $use->name->toString() !== 'App\Models\Media') {
                    return true;
                }
            }
        }

        return false;
    }

    private function addUseImport(Namespace_ $namespace, string $fullyQualifiedName): void
    {
        $newUse = new Use_([new UseItem(new Name($fullyQualifiedName))]);

        // Find the last use statement and insert after it
        $lastUseIndex = null;

        foreach ($namespace->stmts as $index => $stmt) {
            if ($stmt instanceof Use_) {
                $lastUseIndex = $index;
            }
        }

        if ($lastUseIndex !== null) {
            array_splice($namespace->stmts, $lastUseIndex + 1, 0, [$newUse]);
        }
    }
}
