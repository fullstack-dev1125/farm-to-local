<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Feature tests run against the real seeders (categories, Texas, Austin, ZIP sample, Austin demo sellers),
     * so geography assertions are about true coordinates. RefreshDatabase seeds once, then wraps each test
     * in a transaction. MySQL only (farmtolocal_test): the radius query needs ST_Distance_Sphere.
     */
    protected bool $seed = true;
}
