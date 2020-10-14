<?php

return [
    //  authenticated_token生命周期(单位:秒)
    'ttl' => env('AUTHENTICATED_TOKEN_TTL', 86400 * 7),
];