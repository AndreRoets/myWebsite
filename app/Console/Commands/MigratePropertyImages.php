<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigratePropertyImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:migrate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates existing property images to a new folder structure (storage/app/public/properties/{id}/image.jpg)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting property image migration...');

        $properties = Property::whereNotNull('hero_image')->get();
        $bar = $this->output->createProgressBar($properties->count());
        $bar->start();

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($properties as $property) {
            $oldPath = $property->hero_image;

            // Skip if it looks like it's already migrated
            if (Str::startsWith($oldPath, "properties/{$property->id}/")) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            $filename = basename($oldPath);
            $newPath = "properties/{$property->id}/{$filename}";

            // Check if the old file exists in the public disk
            if (Storage::disk('public')->exists($oldPath)) {
                // Ensure the new directory exists
                Storage::disk('public')->makeDirectory("properties/{$property->id}");

                // Move the file
                if (Storage::disk('public')->move($oldPath, $newPath)) {
                    // Update the database
                    $property->hero_image = $newPath;
                    $property->save();
                    $migratedCount++;
                } else {
                    $this->error("\nFailed to move file for property #{$property->id}");
                }
            } else {
                 $this->comment("\nSkipping property #{$property->id}: Source file not found at '{$oldPath}'");
                 $skippedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n\nMigration complete!");
        $this->info("{$migratedCount} properties migrated.");
        $this->info("{$skippedCount} properties skipped (no image or already migrated).");

        return 0;
    }
}