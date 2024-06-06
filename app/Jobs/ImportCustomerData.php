<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCustomerData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $chunkData;

    public function __construct(array $chunkData)
    {
        $this->chunkData = $chunkData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->chunkData)) {
            Log::warning('Received an empty chunk, skipping.');
            return;
        }
    
        $insertData = [];
        $timestamp = now();
        $rowCount = 0;
    
        foreach ($this->chunkData as $column) {
            $insertData[] = [
                'customer_id' => $column[1],
                'fname' => $column[2],
                'lname' => $column[3],
                'company' => $column[4],
                'city' => $column[5],
                'country' => $column[6],
                'phone_first' => $column[7],
                'phone_second' => $column[8],
                'email' => $column[9],
                'subscription_date' => $column[10],
                'website' => $column[11],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
            $rowCount++;
        }
        DB::transaction(function () use ($insertData) {
            Customer::insert($insertData);
        });
    
        Log::info('Inserted a chunk of data', ['rowCount' => $rowCount]);
    }
}
