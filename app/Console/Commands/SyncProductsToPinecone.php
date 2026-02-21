<?php

namespace App\Console\Commands;

use App\Services\PineconeService;
use Illuminate\Console\Command;

class SyncProductsToPinecone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-pinecone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all products to Pinecone vector database';

    /**
     * Execute the console command.
     */
    public function handle(PineconeService $pineconeService)
    {
        $this->info('Starting product synchronization to Pinecone...');

        $result = $pineconeService->syncAllProducts();

        $this->info("Synchronization completed!");
        $this->info("Total products: {$result['total']}");
        $this->info("Successfully synced: {$result['synced']}");
        
        if ($result['failed'] > 0) {
            $this->warn("Failed to sync: {$result['failed']}");
        }

        return Command::SUCCESS;
    }
}
