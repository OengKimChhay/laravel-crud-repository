<?php

namespace Database\Factories\Modules\Category;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Category\Category;
use App\Helper\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name()
        ];
    }


}
