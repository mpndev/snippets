<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function abilities() {
        return $this->belongsToMany(Ability::class)->withTimestamps();
    }

    public function addAbilityTo($ability) {
        if (is_string($ability)) {
            if (strpos($ability, ' ') !== false) {
                throw new \Exception("Ability name cannot use spaces. Use underscores instead!");
            }
            $ability = Ability::firstOrNew([
                'name' => $ability,
            ]);
            if (!$ability->label) {
                $ability->label = implode(' ', array_map(function ($word) {
                    return ucfirst($word);
                }, (explode('_', $ability->name))));
                $ability->save();
            }
        }
        $this->abilities()->sync($ability, false);
        return $this;
    }
}
