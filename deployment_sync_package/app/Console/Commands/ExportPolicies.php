<?php

namespace App\Console\Commands;

use App\Models\Policy;
use Illuminate\Console\Command;

class ExportPolicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policies:export {--output=policies_export.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all policies to JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('====================================');
        $this->info('Policy Export Tool');
        $this->info('====================================');
        $this->newLine();

        $outputFile = $this->option('output');
        $outputPath = base_path($outputFile);

        $this->info('📊 Counting policies...');
        $totalPolicies = Policy::count();
        $this->info("   Found: {$totalPolicies} policies");
        $this->newLine();

        if ($totalPolicies == 0) {
            $this->warn('⚠️  No policies found to export!');
            return 1;
        }

        $this->info('📥 Exporting policies...');
        
        $policies = Policy::all()->toArray();

        $this->info('📥 Exporting renewals...');
        $renewals = \App\Models\Renewal::all()->toArray();

        $export = [
            'exported_at' => now()->toDateTimeString(),
            'total_policies' => count($policies),
            'total_renewals' => count($renewals),
            'server' => env('APP_ENV', 'unknown'),
            'policies' => $policies,
            'renewals' => $renewals
        ];

        $this->info('💾 Writing to file...');
        file_put_contents($outputPath, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $fileSize = filesize($outputPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        $this->newLine();
        $this->info('✅ Export completed successfully!');
        $this->info("   File: {$outputPath}");
        $this->info("   Size: {$fileSizeMB} MB");
        $this->info("   Policies: {$totalPolicies}");
        $this->newLine();

        return 0;
    }
}

