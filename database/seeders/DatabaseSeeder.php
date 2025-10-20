<?php

public function run(): void
{
    \App\Models\Property::factory()->count(24)->create();
}
