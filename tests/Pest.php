<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(
    Tests\TestCase::class,
    RefreshDatabase::class,
)->in('Feature', '../Modules/Catalog/tests/Feature', '../Modules/Order/tests/Feature');
