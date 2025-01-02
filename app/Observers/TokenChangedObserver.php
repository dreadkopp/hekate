<?php

namespace App\Observers;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Cache;

class TokenChangedObserver
{
    public function saved(AccessToken $accessToken) :void
    {
        $this->clearCache($accessToken);
    }

    public function deleted(AccessToken $accessToken) :void
    {
            $this->clearCache($accessToken);
    }

    protected function clearCache(AccessToken $accessToken) :void
    {

        $key = 'auth:token:' . $accessToken->getKey();

        Cache::store('apc')->forget($key);
        Cache::forget($key);
    }
}
