<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class PilotSpaceshipsFilter implements EntityListSelectFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Spaceship::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }
}