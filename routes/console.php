<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('firm:remind-hearings')->dailyAt('08:00');
Schedule::command('firm:remind-deadlines')->dailyAt('08:15');
Schedule::command('firm:mark-overdue-invoices')->dailyAt('00:30');
