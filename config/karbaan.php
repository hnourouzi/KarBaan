<?php

return [

    /*
    |--------------------------------------------------------------------------
    | End-of-Day Reminder Time
    |--------------------------------------------------------------------------
    |
    | Employees with an open daily plan see an in-panel reminder after this
    | time (24-hour HH:MM, app timezone).
    |
    */

    'end_of_day_reminder_time' => env('KARBAAN_END_OF_DAY_REMINDER_TIME', '18:00'),

    /*
    |--------------------------------------------------------------------------
    | Work Sessions
    |--------------------------------------------------------------------------
    |
    | An employee may clock in more than once on the same daily plan. A hard
    | cap prevents accidental loops (forgotten clock-outs / repeated starts).
    |
    */

    'max_work_sessions_per_day' => 10,

    /*
    |--------------------------------------------------------------------------
    | PDF Export Driver
    |--------------------------------------------------------------------------
    |
    | "browsershot" (default) uses headless Chrome via Puppeteer and renders
    | Persian/RTL text correctly. Requires Node.js and `npm install` (Puppeteer).
    | "dompdf" is kept as a fallback but does not shape Persian reliably.
    |
    */

    'pdf_driver' => env('KARBAAN_PDF_DRIVER', 'browsershot'),

    'node_binary' => env('NODE_BINARY'),

    'npm_binary' => env('NPM_BINARY'),

    'chrome_path' => env('CHROME_PATH'),

];
