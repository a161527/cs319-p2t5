<?php

use Illuminate\Database\Seeder;

use App\Residence;
use App\RoomSet;
use App\RoomType;

class RoomSeeder extends Seeder
{

    const TARGET_CONF = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $residences = array(
            ["name" => "Foobar Res",
             "location" => "Integer Drive",
             "conferenceID" => self::TARGET_CONF],
            ["name" => "Barbaz Res",
             "location" => "XYZ Ave",
             "conferenceID" => self::TARGET_CONF]);

        foreach ($residences as $res) {
            Residence::create($res);
        }

        $types = array(
            ["name" => "TypeA",
             "capacity" => 4,
             "accessible" => true],
            ["name" => "TypeB",
             "capacity" => 4,
             "accessible" => false],
            ["name" => "TypeA-R2",
             "capacity" => 4,
             "accessible" => true]);

        foreach ($types as $ty) {
            RoomType::create($ty);
        }

        $sets = array(
            ["name" => "104A",
             "residenceID" => 1,
             "typeID" => 1],
            ["name" => "204B",
             "residenceID" => 1,
             "typeID" => 2],
            ["rangeStart" => 10,
             "rangeEnd" => 19,
             "residenceID" => 2,
             "typeID" => 3]);

        foreach ($sets as $set) {
            RoomSet::create($set);
        }
    }
}
