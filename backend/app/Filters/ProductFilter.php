<?php 

namespace App\Filters;

class ProductFilter extends QueryFilter {
    protected array $allowedFilters = ['name', 'barcode', 'is_active', 'category_id'];

    public function is_active(string $is_active): void {
        $boolean = filter_var($is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean !== null) {
            $this->builder->where('is_active', $boolean);
        }
    }

    public function barcode(string $barcode): void {
        $this->builder->where('barcode', $barcode);
    }

    public function name(string $name): void {
        $this->builder->where('name', 'ilike', '%' . $name . '%');
    }

    public function category_id(string $categoryId): void {
        $this->builder->where('category_id', $categoryId);
    }
}