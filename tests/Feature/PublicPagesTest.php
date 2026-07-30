<?php

it('renders the public information pages with the shared footer', function (string $route): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Resume Studio')
        ->assertSee('Privacy policy')
        ->assertSee('Terms of service');
})->with(['about', 'contact', 'privacy', 'terms']);
