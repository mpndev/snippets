<?php

namespace App\Traits;

trait Searchable
{
    /**
     * Look for models, that contains string in his fields, by priority.
     * @param $query
     * @param $needle
     * @return mixed
     */
    public function scopeSearch($query, $needle)
    {
        if (isset($needle)) {
            $is_first = true;
            foreach($this->searchable_fields as $field) {
                if ($is_first) {
                    $query = $query->where($field['name'], 'like', "%$needle%");
                    $is_first = false;
                }
                else {
                    $query = $query->orWhere($field['name'], 'like', "%$needle%");
                }
            }
        }
        return $query;
    }

}
