<?php

use App\Jobs\GetMailJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new GetMailJob)->everyTenMinutes();
