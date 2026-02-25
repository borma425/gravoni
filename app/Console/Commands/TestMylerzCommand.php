<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Plugins\Shipping\Mylerz\MylerzClient;

class TestMylerzCommand extends Command
{
    protected $signature = 'mylerz:test';
    protected $description = 'Test Mylerz connection and list warehouses';

    public function handle(): int
    {
        $this->info('Testing Mylerz connection...');

        if (!config('plugins.mylerz.enabled')) {
            $this->error('MYLERZ_ENABLED is false in .env');
            return 1;
        }

        if (empty(config('plugins.mylerz.username')) || empty(config('plugins.mylerz.password'))) {
            $this->error('MYLERZ_USERNAME and MYLERZ_PASSWORD are required in .env');
            return 1;
        }

        $client = new MylerzClient();

        $this->info('Authenticating...');
        if (!$client->ensureAuthenticated()) {
            $this->error('Authentication failed. Check credentials in .env');
            $this->info('Run: php artisan config:clear');
            return 1;
        }
        $this->info('Authentication successful!');

        $warehouses = $client->getWarehouses();
        if (empty($warehouses)) {
            $this->warn('Could not fetch warehouses');
            return 0;
        }

        $this->info('Available warehouses:');
        foreach ($warehouses as $w) {
            $mark = ($w === config('plugins.mylerz.warehouse')) ? ' <-- current' : '';
            $this->line("  - {$w}{$mark}");
        }

        $current = config('plugins.mylerz.warehouse');
        if (!in_array($current, $warehouses)) {
            $this->warn("MYLERZ_WAREHOUSE='{$current}' is not in the list. Update .env with one of the above.");
        }

        $this->info('');
        $this->info('Mylerz connection OK.');
        return 0;
    }
}
