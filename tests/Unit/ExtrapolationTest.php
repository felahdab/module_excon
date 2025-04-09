<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);
uses(Tests\TestCase::class);

use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Position;

use Illuminate\Support\Carbon;

pest()->group("Excon");

it('reports a position when close enough in time', function() {
    $identifier= new Identifier(["source"=> "COT", 
                                    "identifier" => "identifier1", 
                                    "unit_id" => null]);
    $identifier->save();
    

    $pos1 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => Carbon::create(2025, 1, 1, 12, 0, 0)
    ]); 
    $pos1->save();

    $pos2 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => Carbon::create(2025, 1, 1, 12, 0, 12)
    ]); 
    $pos2->save();

    $identifier->refresh();

    $test_timestamp = Carbon::create(2025, 1, 1, 12, 0, 6);

    [$latitude, $longitude ] = $identifier->extrapolatePositionForTimestamp($test_timestamp);

    $this->assertTrue($latitude == 43 && $longitude == 5);


});

it('reports a the previous position when close enough in time', function() {
    $identifier= new Identifier(["source"=> "COT", 
                                    "identifier" => "identifier1", 
                                    "unit_id" => null]);
    $identifier->save();
    

    $pos1 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => Carbon::create(2025, 1, 1, 12, 0, 0)
    ]); 
    $pos1->save();

    $pos2 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 44,
        "longitude"=> 6,
        "timestamp" => Carbon::create(2025, 1, 1, 12, 0, 12)
    ]); 
    $pos2->save();

    $identifier->refresh();

    $test_timestamp = Carbon::create(2025, 1, 1, 12, 0, 6);

    [$latitude, $longitude ] = $identifier->extrapolatePositionForTimestamp($test_timestamp);

    $this->assertTrue($latitude == 43 && $longitude == 5);


});

it('reports a the next position when the previous one is invalid in time', function() {
    $timestamp1 = Carbon::create(2025, 1, 1, 11, 0, 0);
    $timestamp2 = Carbon::create(2025, 1, 1, 12, 0, 12);
    $test_timestamp = Carbon::create(2025, 1, 1, 12, 0, 6);


    $identifier= new Identifier(["source"=> "COT", 
                                    "identifier" => "identifier1", 
                                    "unit_id" => null]);
    $identifier->save();
    
    $pos1 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => $timestamp1
    ]); 
    $pos1->save();

    $pos2 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 44,
        "longitude"=> 6,
        "timestamp" => $timestamp2
    ]); 
    $pos2->save();

    $identifier->refresh();

    [$latitude, $longitude ] = $identifier->extrapolatePositionForTimestamp($test_timestamp);

    $this->assertTrue($latitude == 44 && $longitude == 6);


});

it('extrapolates between two positions when both valid and above extrapolation threshold', function() {
    $timestamp1 = Carbon::create(2025, 1, 1, 12, 0, 0);
    $timestamp2 = Carbon::create(2025, 1, 1, 12, 0, 30);
    $test_timestamp = Carbon::create(2025, 1, 1, 12, 0, 15);


    $identifier= new Identifier(["source"=> "COT", 
                                    "identifier" => "identifier1", 
                                    "unit_id" => null]);
    $identifier->save();
    
    $pos1 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 43,
        "longitude"=> 5,
        "timestamp" => $timestamp1
    ]); 
    $pos1->save();

    $pos2 = new Position(["identifier_id" => $identifier->id,
        "latitude" => 44,
        "longitude"=> 6,
        "timestamp" => $timestamp2
    ]); 
    $pos2->save();

    $identifier->refresh();

    [$latitude, $longitude ] = $identifier->extrapolatePositionForTimestamp($test_timestamp);

    $this->assertTrue($latitude == 43.5 && $longitude == 5.5);


});