<?php

it('renders the public information pages with the shared footer', function (string $route): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Resume Studio')
        ->assertSee('Privacy policy')
        ->assertSee('Terms of service');
})->with(['about', 'contact', 'pricing', 'privacy', 'terms']);

it('renders the public pricing page with plans, comparison, and frequently asked questions', function (): void {
    $this->get(route('pricing'))
        ->assertOk()
        ->assertSee('Simple, transparent pricing')
        ->assertSee('Career Pass')
        ->assertSee('A clear view of what you can do.')
        ->assertSee('Questions before you begin?');
});
