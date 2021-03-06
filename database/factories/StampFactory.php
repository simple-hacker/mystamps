<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Stamp;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Stamp::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(4),
        'sg_number' => $faker->randomNumber(4),
        'description' => $faker->paragraph(),
        'price' => $faker->randomFloat(2, 0, 5),
        'issue_id' => function () {
            return factory('App\Issue')->create()->id;
        },
        'image' => UploadedFile::fake()->image('stamp.jpg'),
    ];
});
