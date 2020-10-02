<?php

namespace App\Traits;

trait AttributableModel
{
    use \Rinvex\Attributes\Traits\Attributable;

    protected function bootIfNotBooted()
    {
        parent::bootIfNotBooted();
        if (!$this->entityAttributeRelationsBooted) {
            if (env('APP_INSTALLED', false)) {
                app(\Rinvex\Attributes\Support\RelationBuilder::class)->build($this);
            }
            $this->entityAttributeRelationsBooted = true;
        }
    }
}
