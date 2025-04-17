<?php

namespace Modules\Excon\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EntityNumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Excon\Models\EntityNumber::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

