<?php

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

it('redirects Filament logout to the shared Laravel login page', function () {
    $response = app(LogoutResponseContract::class)->toResponse(request());

    expect($response)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe(route('login'));
});
