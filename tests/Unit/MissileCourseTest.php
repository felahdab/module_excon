<?php

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

it('calculates the missile course properly 1', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(0, 10, 90, 10) == 0);

})->only();

it('calculates the missile course properly 2', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(0, 10, 90, 20) == 60);

})->only();

it('calculates the missile course properly 3', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(180, 10, 90, 20) == 120);

})->only();

it('calculates the missile course properly 4', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(180, 10, 90, 10) == 180  );

})->only();

it('calculates the missile course properly 5', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(0, 10, 270, 10) == 360 );

})->only();

it('calculates the missile course properly 6', function() {

    $e = new Engagement;

    $this->assertTrue($e->calcMissileRoute(0, 10, 270, 20) == 300  );

})->only();