<?php

use App\Code;
use Illuminate\Database\Seeder;

class CodesSeeder extends Seeder
{

    /*

    Run with "php artisan db:seed --class CodesSeeder"

    */

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0;$i<1000;$i++) {
            $x = new Code([
                "handout_code"=>"SomeName".rand()
            ]);
            if (rand(0,1)==0) {
                $x->general_location="TestGeneralLocation";
            } else {
                $x->longitude = rand(-180,180);
                $x->latitude = rand(-180,180);
            }
            $x->save();
        }
        for ($i=0;$i<100;$i++) {
            $x = new Code([
                "handout_code"=>"unset".$i
            ]);
            $x->save();
        }

    }
}
