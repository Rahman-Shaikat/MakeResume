<?php

it('renders the public information pages with the shared footer', function (string $route): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('Resume Engineer')
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

it('shows direct phone and Gmail links within the contact panel', function (): void {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('+880 1736 769157')
        ->assertSee('makeresume@gmail.com')
        ->assertDontSee('Gmail support')
        ->assertDontSee('Your details are handled with care');
});
