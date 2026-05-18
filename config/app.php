<?php

return [
    'APP_NAME' => 'Library Management System',
    'APP_VERSION' => '1.0.0',
    'APP_ENV' => getenv('APP_ENV') ?: 'development',
    'APP_DEBUG' => getenv('APP_DEBUG') ?: false,
    'APP_URL' => getenv('APP_URL') ?: 'http://localhost',
    'JWT_SECRET' => getenv('JWT_SECRET'),
    'JWT_EXPIRATION' => getenv('JWT_EXPIRATION') ?: 86400,
    'TIMEZONE' => 'UTC',
    'DATE_FORMAT' => 'Y-m-d',
    'TIME_FORMAT' => 'H:i:s',
];
