<?php namespace Rainlab\Location\Updates;

use Db;
use Log;
use Schema;
use Exception;
use October\Rain\Database\Updates\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SeedNoMunicipalities extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        Db::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Truncate the table safely
            Db::table('rainlab_location_municipalities')->delete();

            // Path to Excel file
            $filePath = plugins_path('rainlab/location/data/PostnummerregisterExcel.xlsx');

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Get all rows as an array
            $rows = $worksheet->toArray(null, true, true, true);

            // Remove header row
            array_shift($rows);

            // Extract unique municipalities
            $uniqueMunicipalities = [];
            foreach ($rows as $row) {
                $municipalityCode = trim($row['C']);
                $municipalityName = trim($row['D']);
                
                // Skip empty rows
                if (empty($municipalityCode) || empty($municipalityName)) continue;
                
                // Use code as key to ensure uniqueness
                $uniqueMunicipalities[$municipalityCode] = $municipalityName;
            }

            // Prepare data for insertion
            $municipalityData = [];
            foreach ($uniqueMunicipalities as $code => $name) {
                $municipalityData[] = [
                    'code' => $code,
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // Batch insert
                if (count($municipalityData) >= 100) {
                    Db::table('rainlab_location_municipalities')->insert($municipalityData);
                    $municipalityData = [];
                }
            }

            // Insert remaining data
            if (!empty($municipalityData)) {
                Db::table('rainlab_location_municipalities')->insert($municipalityData);
            }

            Log::info('Municipalities imported successfully', [
                'total' => count($uniqueMunicipalities)
            ]);

        } catch (Exception $e) {
            Log::error('Municipality Import Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        } finally {
            // Re-enable foreign key checks
            Db::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
