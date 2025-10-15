<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'name' => $this->faker->word(),
			'price' => $this->faker->numberBetween(5000, 50000),
			'stock' => $this->faker->numberBetween(1, 100),
			'type' => $this->faker->randomElement(['Main Course', 'Snack', 'Beverage']),
		];
	}
}
