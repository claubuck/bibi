<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'product_name' => $this->faker->randomElement([
                'iPhone 15', 'Samsung Galaxy S24', 'MacBook Pro', 
                'AirPods Pro', 'iPad Pro', 'Apple Watch'
            ]),
            'product_category' => $this->faker->randomElement([
                'Electrónicos', 'Computación', 'Wearables', 'Accesorios'
            ]),
            'unit_price' => $this->faker->randomFloat(2, 100, 2000),
            'quantity' => $this->faker->numberBetween(1, 5),
            'total_sale' => function (array $attributes) {
                return $attributes['unit_price'] * $attributes['quantity'];
            },
            'customer_type' => $this->faker->randomElement(['Minorista', 'Mayorista']),
            'payment_method' => $this->faker->randomElement([
                'Tarjeta Crédito', 'Transferencia', 'Efectivo', 'PayPal'
            ]),
            'region' => $this->faker->state,
            'sales_person' => $this->faker->name,
        ];
    }
}
