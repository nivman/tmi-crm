<?php

namespace App\Traits;

use Rinvex\Attributes\Support\RelationBuilder;

trait AttributableModel
{
    use \Rinvex\Attributes\Traits\Attributable;

    protected function bootIfNotBooted()
    {
        parent::bootIfNotBooted();

        if (!$this->entityAttributeRelationsBooted) {

                app(RelationBuilder::class)->build($this);

            $this->entityAttributeRelationsBooted = true;
        }
    }
}
