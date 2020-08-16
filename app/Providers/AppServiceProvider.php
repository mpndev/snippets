<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
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
        $event_types = ['saved', 'updated', 'deleted'];
        $flushable_models = ['Snippet', 'User', 'Tag', 'FavoriteSnippets', 'Actions'];
        foreach ($event_types as $event_type) {
            foreach ($flushable_models as $flushable_model) {
                Event::listen('eloquent.' . $event_type . ': App\\' . $flushable_model, function() {
                    Cache::flush();
                });
            }
        }

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

                return $this->sort(function($item1, $item2) use ($search_needle, $fields) {
                    foreach($fields as $field) {
                        $position_first = strpos($item1->{$field['name']}, $search_needle);
                        $position_second = strpos($item2->{$field['name']}, $search_needle);

                        if (!is_numeric($position_first) && !is_numeric($position_second)) {
                            continue;
                        }
                        if (!is_numeric($position_first)) {
                            return -1;
                        }
                        if (!is_numeric($position_second)) {
                            return 1;
                        }
                        if ($position_first === $position_second) {
                            return 1;
                        }

                        return $position_first < $position_second ? -1 : 1;
                    }

                    return 0;
                });
            }

            return $this;
        });
    }
}
