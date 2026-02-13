<?php

namespace App\Socialite;

use SocialiteProviders\VKontakte\Provider;

class VKIdProvider extends Provider
{
    /**
     * The executing provider.
     */
    public const IDENTIFIER = 'VKONTAKTE';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://id.vk.com/oauth2/auth', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://id.vk.com/oauth2/auth';
    }

    protected function usesPKCE(): bool
    {
        // VK ID recommends PKCE
        return true;
    }
}
