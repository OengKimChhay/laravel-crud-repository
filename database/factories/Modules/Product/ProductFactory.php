<?php

namespace Database\Factories\Modules\Product;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Product\Product;
use App\Helper\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->city,
            'code' => Generator::productCodeGenerator('XT'),
            'image' => $this->faker->imageUrl()
        ];
    }
}
