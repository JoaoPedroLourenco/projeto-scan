<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class QueryFilter {
    public Request $request;
    public Builder $builder;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function apply(Builder $builder) {
        $this->builder = $builder;

        foreach ($this->request->all() as $name => $value) {
            if ($this->isValidParam($value) && method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }

    public function isValidParam($value): bool {
        if (is_array($value)) {
            return !empty(array_filter($value));
        }

        return $value !== null && $value !== '';
    }
}