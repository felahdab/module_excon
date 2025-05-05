<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);
uses(Tests\TestCase::class);

use Illuminate\Support\Carbon;

use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Side;
use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Position;
use Modules\Excon\Models\EntityNumber;


pest()->group("Excon");

beforeEach(function () {
    
    $this->timestamp = now();
    
    $this->blue_side= Side::create(["name" => "blue"]);
    $this->red_side = Side::create(["name" => "red"]);

    $this->warship1 = Unit::create(["name" => "Warship 1", "side_id" => $this->blue_side->id]);
    $this->warship2 = Unit::create(["name" => "Warship 2", "side_id" => $this->red_side->id]);
    
    $this->own_id_wharship1 = Identifier::create(["source"=> "LDT", 
                                        "identifier" => "0043", 
                                        "unit_id" => $this->warship1->id]);
    
    $this->other_id_wharship1 = Identifier::create(["source"=> "LDT", 
                                        "identifier" => "5432", 
                                        "unit_id" => null]);

    $this->own_id_wharship2 = Identifier::create(["source"=> "LDT", 
                                        "identifier" => "0052", 
                                        "unit_id" => $this->warship2->id]);  
                                        
    $this->other_id_wharship2 = Identifier::create(["source"=> "LDT", 
                                        "identifier" => "1234", 
                                        "unit_id" => null]);

    $this->own_pos1 = Position::create(
        ["identifier_id" => $this->own_id_wharship1->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => $this->timestamp
        ]
    );

    $this->other_pos1 = Position::create(
        ["identifier_id" => $this->other_id_wharship1->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => $this->timestamp
        ]
    );

    $this->own_pos2 = Position::create(
        ["identifier_id" => $this->own_id_wharship2->id,
        "latitude" => 44,
        "longitude"=> 5,
        "timestamp" => $this->timestamp
        ]
    ); 

    $this->other_pos2 = Position::create(
        ["identifier_id" => $this->other_id_wharship2->id,
        "latitude" => 44,
        "longitude"=> 5,
        "timestamp" => $this->timestamp
        ]
    ); 

    $this->weapon = Weapon::firstOrCreate(
        ["name" => "MM 40"], 
        [
            "kind" => 2,
            "domain" => 6,
            "country" => 71,
            "category" => 1,
            "subcategory" => 1,
            "specific" => 4,
            "extra" => 0,
            "speed" => 600
        ]);    
});

it('test engagement 1', function() {
    $engagement = Engagement::create(
        [
            "timestamp" => $this->timestamp,
            "unit_id" => $this->warship1->id,
            "weapon_id" => $this->weapon->id,
            "entity_number" => EntityNumber::getNewEntityNumber(),
            "amount" => 1,
            "data" => [
                "engagement_type" => "absolute_position",
                "target_latitude" => 44,
                "target_longitude" => 5
            ]
        ]
    );

    $description = $engagement->description_for_dis();

    $this->assertTrue($description["course"] == 0);

});

it('test engagement 2', function() {
    $engagement = Engagement::create(
        [
            "timestamp" => $this->timestamp,
            "unit_id" => $this->warship1->id,
            "weapon_id" => $this->weapon->id,
            "entity_number" => EntityNumber::getNewEntityNumber(),
            "amount" => 1,
            "data" => [
                "engagement_type" => "absolute_position",
                "target_latitude" => 43,
                "target_longitude" => 6
            ]
        ]
    );

    $description = $engagement->description_for_dis();

    $this->assertTrue(intval($description["course"] * 1000) / 1000 == 89.658);

});

it('test engagement 3', function() {
    //dump(Position::all());
    $engagement = Engagement::create(
        [
            "timestamp" => $this->timestamp,
            "unit_id" => $this->warship1->id,
            "weapon_id" => $this->weapon->id,
            "entity_number" => EntityNumber::getNewEntityNumber(),
            "amount" => 1,
            "data" => [
                "engagement_type" => "absolute_position",
                "target_latitude" => 43,
                "target_longitude" => 4
            ]
        ]
    );

    $description = $engagement->description_for_dis();
    //dump($description);
    $this->assertTrue(intval($description["course"] * 1000) / 1000 == 271);

});

it('test engagement 4', function() {
    $engagement = Engagement::create(
        [
            "timestamp" => $this->timestamp,
            "unit_id" => $this->warship1->id,
            "weapon_id" => $this->weapon->id,
            "entity_number" => EntityNumber::getNewEntityNumber(),
            "amount" => 1,
            "data" => [
                "engagement_type" => "track_number",
                "track_number" => "1234",
            ]
        ]
    );

    $description = $engagement->description_for_dis();
    //dump($description);
    $this->assertTrue(intval($description["course"] * 1000) / 1000 == 0);
});

it('test engagement 5', function() {
    $this->other_pos2->latitude = 43;
    $this->other_pos2->longitude = 6;
    $this->other_pos2->save();

    $engagement = Engagement::create(
        [
            "timestamp" => $this->timestamp,
            "unit_id" => $this->warship1->id,
            "weapon_id" => $this->weapon->id,
            "entity_number" => EntityNumber::getNewEntityNumber(),
            "amount" => 1,
            "data" => [
                "engagement_type" => "track_number",
                "track_number" => "1234",
            ]
        ]
    );

    $description = $engagement->description_for_dis();
    //dump($description);
    $this->assertTrue(intval($description["course"] * 1000) / 1000 == 89.658);
    //$this->assertTrue(true);

});