<?php

namespace Uteq\Move\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Uteq\Move\Tests\Fixtures\Contact;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'postcode' => $this->faker->postcode,
            'house_number' => $this->faker->numberBetween(0, 999),
            'street' => $this->faker->streetName,
        ];
    }
}
