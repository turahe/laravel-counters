<?php

declare(strict_types=1);

namespace Turahe\Counters\Commands;

use Illuminate\Console\Command;
use Turahe\Counters\Classes\Counters;
use Turahe\Counters\Exceptions\CounterAlreadyExists;

/**
 * Optimized MakeCounter command for PHP 8.4 and Laravel 11/12.
 */
class MakeCounter extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:counter 
        {key : The unique key for the counter}
        {name : The display name for the counter}
        {--initial-value=0 : The initial value for the counter}
        {--step=1 : The step value for increment/decrement operations}
        {--notes= : Optional notes for the counter}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new counter with the specified parameters';

    /**
     * Execute the console command.
     */
    public function handle(Counters $counters): int
    {
        $this->info('Creating counter...');

        try {
            $key = $this->argument('key');
            $name = $this->argument('name');
            $initialValue = (int) $this->option('initial-value');
            $step = (int) $this->option('step');
            $notes = $this->option('notes');

            // Validate inputs
            if (empty($key)) {
                $this->error('Counter key is required.');
                return self::FAILURE;
            }

            if (empty($name)) {
                $this->error('Counter name is required.');
                return self::FAILURE;
            }

            // Create the counter
            $counter = $counters->create(
                key: $key,
                name: $name,
                initialValue: $initialValue,
                step: $step,
                notes: $notes
            );

            $this->info("✅ Counter '{$key}' created successfully!");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Key', $counter->key],
                    ['Name', $counter->name],
                    ['Initial Value', $counter->initial_value],
                    ['Current Value', $counter->value],
                    ['Step', $counter->step],
                    ['Notes', $counter->notes ?? 'N/A'],
                    ['Created At', $counter->created_at->format('Y-m-d H:i:s')],
                ]
            );

            return self::SUCCESS;
        } catch (CounterAlreadyExists $e) {
            $this->error("❌ Counter '{$key}' already exists!");
            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create counter: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
