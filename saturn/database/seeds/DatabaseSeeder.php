<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->create([
            "email" => "boss@example.com",
            "group" => "sharp,boss"
        ]);

        factory(\App\User::class)->create([
            "email" => "admin@example.com",
            "group" => "sharp"
        ]);

        factory(\App\User::class)->create([
            "email" => "user@example.com",
            "group" => "user"
        ]);

        $types = factory(\App\SpaceshipType::class, 5)->create();

        $features = factory(\App\Feature::class, 15)->create();

        $pilots = factory(\App\Pilot::class, 50)->create();

        foreach($types as $type) {
            $spaceships = factory(\App\Spaceship::class, 10)->create([
                "type_id" => $type->id
            ]);

            foreach($spaceships as $spaceship) {
                factory(\App\TechnicalReview::class, rand(1, 2))->create([
                    "spaceship_id" => $spaceship->id
                ]);

                if($spaceship->id%2 == 0) {
                    $travel = factory(\App\Travel::class)->create([
                        "spaceship_id" => $spaceship->id
                    ]);

                    $passengers = factory(\App\Passenger::class, rand(10, 200))->create([
                        "travel_id" => $travel->id
                    ]);

                    $travel->delegates()->sync(
                        $passengers->random(rand(1, 3))->pluck("id")->all()
                    );
                }

                $spaceship->pilots()->sync(
                    $pilots->random(rand(1, 3))->pluck("id")->all()
                );

                $spaceship->features()->sync(
                    $features->random(rand(1, 3))->pluck("id")->all()
                );
            }
        }
    }
}
