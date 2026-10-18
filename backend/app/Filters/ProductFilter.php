<?php 

namespace App\Filters;

class ProductFilter extends QueryFilter{
    public function is_active(string $is_active): void {
        $this->builder->where('is_active', $is_active);
    }

    public function barcode(string $barcode): void {
        $this->builder->where('barcode', $barcode);
    }

    public function name(string $name): void {
        $this->builder->where('name', 'ilike', '%' . $name . '%');
    }
}