<?php

return [
    'test_mode' => env('TURBOSMS_TEST_MODE', true),

    /**
     * @see https://turbosms.ua/route.html
     */
    'api_key' => env('TURBOSMS_API_KEY', 'test_api_key'),

    /**
     * @see https://turbosms.ua/sign.html
     * Supported: "MAGAZIN", "Market", "TAXI", "SERVIS TAXI",
     *            "Dostavka24", "IT Alarm", "Post Master"
     */
    'sms_sender' => env('TURBOSMS_SMS_SENDER', 'Market'),

    /**
     * @see https://turbosms.ua/viber/show/senders.html
     * Supported: "Mobibon"
     */
    'viber_sender' => env('TURBOSMS_VIBER_SENDER', 'Mobibon'),

    'sleep_mode' => false,
    'min_hour' => 9,
    'max_hour' => 21,

    /**
     * HTTP client settings.
     *
     * http_response_timeout - maximum number of seconds to wait for a response
     * http_retry_max_time - the maximum number of times the request should be attempted
     * http_retry_delay - the number of milliseconds that Laravel should wait in between attempts
     */
    'http_response_timeout' => 3,
    'http_retry_max_time' => 2,
    'http_retry_delay' => 200,

];
