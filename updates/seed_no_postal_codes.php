<?php namespace Rainlab\Location\Updates;

use Db;
use Log;
use Schema;
use Exception;
use October\Rain\Database\Updates\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rainlab\Location\Models\Municipality;
use Rainlab\Location\Models\PostalCode;

class SeedNoPostalCodes extends Seeder
{
    public function run()
    {
        // Ensure municipalities exist first
        if (Municipality::count() == 0) {
            Log::warning('No municipalities found. Please run SeedNoMunicipalities first.');
            return;
        }

        // Disable foreign key checks
        Db::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Truncate the table safely
            PostalCode::truncate();

            // Path to Excel file
            $filePath = plugins_path('rainlab/location/data/PostnummerregisterExcel.xlsx');

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Get all rows as an array
            $rows = $worksheet->toArray(null, true, true, true);

            // Remove header row
            array_shift($rows);
            
            // Process in batches to avoid memory issues
            $batchSize = 500;
            $currentBatch = [];
            $processed = 0;
            $total = count($rows);
            
            foreach ($rows as $row) {
                // Skip empty rows
                if (empty($row['A'])) continue;

                $postalCode = trim($row['A']);
                $postalName = trim($row['B']);
                $municipalityCode = trim($row['C']);

                // Find the municipality using Eloquent
                $municipality = Municipality::where('code', $municipalityCode)->first();
                
                if ($municipality) {
                    $currentBatch[] = [
                        'code' => $postalCode,
                        'name' => $postalName,
                        'municipality_id' => $municipality->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                } else {
                    Log::warning("Municipality not found for code: $municipalityCode");
                    // Still create the postal code without municipality link
                    $currentBatch[] = [
                        'code' => $postalCode,
                        'name' => $postalName,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                // Process in batches
                if (count($currentBatch) >= $batchSize) {
                    PostalCode::insert($currentBatch);
                    $processed += count($currentBatch);
                    $currentBatch = [];
                    
                    // Log progress
                    $percentage = round(($processed / $total) * 100);
                    Log::info("Processed $processed of $total postal codes ($percentage%)");
                }
            }
            
            // Insert any remaining records
            if (count($currentBatch) > 0) {
                PostalCode::insert($currentBatch);
                $processed += count($currentBatch);
            }

            $unlinkedCount = PostalCode::whereNull('municipality_id')->count();
            
            Log::info('Postal codes import completed', [
                'total_processed' => $processed,
                'unlinked_postal_codes' => $unlinkedCount
            ]);

        } catch (Exception $e) {
            Log::error('Postal Code Import Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Re-enable foreign key checks
            Db::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
