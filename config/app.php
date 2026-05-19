<?php

return [
    'APP_ENV' => getenv('APP_ENV') ?: 'development',
    'APP_DEBUG' => getenv('APP_DEBUG') ?: false,
    'APP_URL' => getenv('APP_URL') ?: 'http://localhost:8000',
    'JWT_SECRET' => getenv('JWT_SECRET') ?: 'your-secret-key-change-this',
    'JWT_EXPIRATION' => (int)(getenv('JWT_EXPIRATION') ?: 86400),
    'UPLOAD_DIR' => getenv('FILE_UPLOAD_PATH') ?: 'uploads/',
    'MAX_FILE_SIZE' => (int)(getenv('MAX_FILE_SIZE') ?: 5242880)
];