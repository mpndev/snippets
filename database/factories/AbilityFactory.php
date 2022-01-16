<?php

namespace Database\Factories;

use App\Models\Ability;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbilityFactory extends Factory {

    protected $model = Ability::class;

    public function definition()
    {
        return [
            'name' => 'manage_users',
            'label' => ucfirst('Manage users'),
        ];
    }

}
