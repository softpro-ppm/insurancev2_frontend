<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Policy;
use App\Models\PolicyVersion;

class CleanupPolicyVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policy:cleanup-versions 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompt}
                            {--all : Delete ALL policy versions (keep only current policy data)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old policy versions, keeping only the latest version for each policy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== POLICY VERSION CLEANUP ===');
        $this->newLine();

        // Get statistics
        $totalPolicies = Policy::count();
        $totalVersions = PolicyVersion::count();
        
        if ($this->option('all')) {
            $policiesWithVersions = Policy::has('versions')->with('versions')->get();
        } else {
            $policiesWithVersions = Policy::has('versions', '>', 1)->with('versions')->get();
        }

        $this->info("BEFORE CLEANUP:");
        $this->line("  Total policies: {$totalPolicies}");
        $this->line("  Total policy versions: {$totalVersions}");
        if ($this->option('all')) {
            $this->line("  Policies with versions: " . $policiesWithVersions->count());
        } else {
            $this->line("  Policies with multiple versions: " . $policiesWithVersions->count());
        }
        $this->newLine();

        if ($policiesWithVersions->isEmpty()) {
            if ($this->option('all')) {
                $this->info('No policies with versions found. Nothing to clean up.');
            } else {
                $this->info('No policies with multiple versions found. Nothing to clean up.');
            }
            return 0;
        }

        // Show what will be deleted
        $this->info('POLICIES TO BE PROCESSED:');
        foreach ($policiesWithVersions as $policy) {
            $versions = $policy->versions()->orderBy('version_number', 'desc')->get();
            
            if ($this->option('all')) {
                $this->line("  Policy ID: {$policy->id} ({$policy->customer_name})");
                $this->line("    Deleting: " . $versions->count() . " versions (keeping only current policy data)");
            } else {
                $latestVersion = $versions->first();
                $versionsToDelete = $versions->skip(1);
                
                $this->line("  Policy ID: {$policy->id} ({$policy->customer_name})");
                $this->line("    Keeping: Version {$latestVersion->version_number}");
                $this->line("    Deleting: " . $versionsToDelete->count() . " older versions");
            }
        }
        $this->newLine();

        // Confirmation
        if (!$this->option('dry-run') && !$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the cleanup?')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        try {
            $totalVersionsToDelete = 0;
            $totalDocumentsToClean = 0;

            if (!$this->option('dry-run')) {
                DB::beginTransaction();
            }

            // Process each policy
            foreach ($policiesWithMultipleVersions as $policy) {
                $this->line("Processing Policy ID: {$policy->id} ({$policy->customer_name})");

                $versions = $policy->versions()->orderBy('version_number', 'desc')->get();
                $latestVersion = $versions->first();
                $versionsToDelete = $versions->skip(1);

                $this->line("  Keeping: Version {$latestVersion->version_number}");
                $this->line("  Deleting: " . $versionsToDelete->count() . " older versions");

                foreach ($versionsToDelete as $version) {
                    $this->line("    Deleting Version {$version->version_number}...");

                    // Count documents
                    $documents = [
                        $version->policy_copy_path,
                        $version->rc_copy_path,
                        $version->aadhar_copy_path,
                        $version->pan_copy_path
                    ];

                    $documentCount = 0;
                    foreach ($documents as $docPath) {
                        if (!empty($docPath) && Storage::exists($docPath)) {
                            $documentCount++;
                        }
                    }

                    if ($documentCount > 0) {
                        $this->line("      Will clean up {$documentCount} documents from this version");
                        $totalDocumentsToClean += $documentCount;
                    }

                    if (!$this->option('dry-run')) {
                        $version->delete();
                    }
                    $totalVersionsToDelete++;
                }

                $this->line("  ✓ Policy {$policy->id} cleanup completed");
                $this->newLine();
            }

            if (!$this->option('dry-run')) {
                DB::commit();
            }

            $this->info('=== CLEANUP SUMMARY ===');
            $this->line("Total versions " . ($this->option('dry-run') ? 'that would be' : '') . " deleted: {$totalVersionsToDelete}");
            $this->line("Total documents " . ($this->option('dry-run') ? 'that would be' : '') . " cleaned up: {$totalDocumentsToClean}");
            $this->line("Policies processed: " . $policiesWithMultipleVersions->count());
            $this->newLine();

            // Verification
            if (!$this->option('dry-run')) {
                $remainingVersions = PolicyVersion::count();
                $policiesWithMultipleVersions = Policy::has('versions', '>', 1)->count();

                $this->info('=== VERIFICATION ===');
                $this->line("AFTER CLEANUP:");
                $this->line("  Total policies: {$totalPolicies}");
                $this->line("  Remaining policy versions: {$remainingVersions}");
                $this->line("  Policies with multiple versions: {$policiesWithMultipleVersions}");

                if ($policiesWithMultipleVersions === 0) {
                    $this->info('✓ SUCCESS: All policies now have only one version (the latest one)');
                } else {
                    $this->warn('⚠ WARNING: Some policies still have multiple versions');
                }
            }

        } catch (\Exception $e) {
            if (!$this->option('dry-run')) {
                DB::rollback();
            }
            $this->error('ERROR: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('=== CLEANUP COMPLETED ===');
        return 0;
    }
}
