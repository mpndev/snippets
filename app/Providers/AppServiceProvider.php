<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('paginate', function(int $perPage = 15, $page = null, $options = []) {
            /** @var Collection $this */
            $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage)->values(),
                $this->count(),
                $perPage,
                $page,
                $options
            );
        });
        Collection::macro('orderByRelevance', function($search_needle = null) {
            /** @var Collection $this */
            if ($search_needle && count($this->items)) {
                $fields = $this->first()->searchable_fields;
                usort($fields, function($a, $b) {
                    return $a['priority'] > $b['priority'];
                });

                foreach($fields as $field) {
                    usort($this->items, function($item1, $item2) use ($search_needle, $field) {
                        $position_first = strpos($item1->{$field['name']}, $search_needle);
                        $position_second = strpos($item2->{$field['name']}, $search_needle);

                        if (!is_numeric($position_first) && !is_numeric($position_second)) {
                            return 0;
                        }
                        if (!is_numeric($position_first)) {
                            return 1;
                        }
                        if (!is_numeric($position_second)) {
                            return -1;
                        }
                        if ($position_first === $position_second) {
                            return 0;
                        }

                        return $position_first > $position_second ? 1 : -1;
                    });
                };
            }
            return $this;
        });

        Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0]);
            $column = $query->getGrammar()->wrap($parameters[1]);
            return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->whereNotIn('id', [$parameters[2]])->count();
        });
    }
}
