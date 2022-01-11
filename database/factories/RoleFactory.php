<?php

namespace Database\Factories;

use App\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory {

    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => 'admin',
            'label' => ucfirst('Administrator'),
        ];
    }

}