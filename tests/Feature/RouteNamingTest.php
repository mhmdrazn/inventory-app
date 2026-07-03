<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteNamingTest extends TestCase
{
    public function test_web_route_names_do_not_collide_with_api_route_names(): void
    {
        $this->assertSame('/products/1', route('products.show', ['product' => 1], false));
        $this->assertSame('/categories/1', route('categories.show', ['category' => 1], false));
        $this->assertSame('/borrowings/1', route('borrowings.show', ['borrowing' => 1], false));

        $this->assertSame('/api/v1/products/1', route('api.v1.products.show', ['product' => 1], false));
        $this->assertSame('/api/v1/categories/1', route('api.v1.categories.show', ['category' => 1], false));
        $this->assertSame('/api/v1/borrowings/1', route('api.v1.borrowings.show', ['borrowing' => 1], false));
    }
}
