<?php

/**
 * Add this route to your routes/web.php file
 * Then visit: https://v2insurance.softpromis.com/cleanup-versions
 */

Route::get('/cleanup', function () {
    use App\Models\Policy;
    use App\Models\PolicyVersion;
    
    $html = '<h1>🧹 Policy Version Cleanup</h1>';
    $html .= '<style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; font-weight: bold; }
        .btn { background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        .btn:hover { background: #c82333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; }
    </style>';
    
    $html .= '<div class="container">';
    
    try {
        $html .= '<h2>🔍 Analyzing Policy Versions...</h2>';
        
        // Get all policies with their version counts
        $allPolicies = Policy::with('versions')->get();
        $policiesWithMultipleVersions = $allPolicies->filter(function ($policy) {
            return $policy->versions->count() > 1;
        });
        
        $totalPolicies = $allPolicies->count();
        $policiesWithDuplicates = $policiesWithMultipleVersions->count();
        $totalVersionsToDelete = $policiesWithMultipleVersions->sum(function ($policy) {
            return $policy->versions->count() - 1;
        });
        
        $html .= '<div class="info">';
        $html .= '<h3>📊 Analysis Results:</h3>';
        $html .= '<p>• Total policies in system: <strong>' . $totalPolicies . '</strong></p>';
        $html .= '<p>• Policies with multiple versions: <strong>' . $policiesWithDuplicates . '</strong></p>';
        $html .= '<p>• Total versions to delete: <strong>' . $totalVersionsToDelete . '</strong></p>';
        $html .= '<p>• Versions to keep: <strong>' . $policiesWithDuplicates . '</strong></p>';
        $html .= '</div>';
        
        if ($policiesWithDuplicates === 0) {
            $html .= '<p class="success">✅ No duplicate versions found. All policies have only 1 version.</p>';
            $html .= '<p>Your system is already clean!</p>';
        } else {
            $html .= '<h3>📋 Sample Policies with Duplicate Versions:</h3>';
            $html .= '<ul>';
            $sampleCount = 0;
            foreach ($policiesWithMultipleVersions as $policy) {
                if ($sampleCount >= 10) {
                    $html .= '<li>... and ' . ($policiesWithDuplicates - 10) . ' more policies</li>';
                    break;
                }
                $html .= '<li>Policy #' . $policy->id . ': <strong>' . $policy->customer_name . '</strong> (' . $policy->versions->count() . ' versions)</li>';
                $sampleCount++;
            }
            $html .= '</ul>';
            
            // Check if cleanup action is requested
            if (request('action') === 'cleanup') {
                $html .= '<h2 class="warning">🗑️ Starting Cleanup Process...</h2>';
                
                $deletedCount = 0;
                $errors = [];
                $processedCount = 0;
                
                foreach ($policiesWithMultipleVersions as $policy) {
                    try {
                        $processedCount++;
                        $html .= '<p>Processing Policy #' . $policy->id . ': <strong>' . $policy->customer_name . '</strong>... (' . $processedCount . '/' . $policiesWithDuplicates . ')</p>';
                        
                        // Delete all versions except version 1
                        $versionsToDelete = PolicyVersion::where('policy_id', $policy->id)
                            ->where('version_number', '>', 1)
                            ->get();
                            
                        foreach ($versionsToDelete as $version) {
                            $html .= '<p style="margin-left: 20px; color: #666;">- Deleting version ' . $version->version_number . ' (created: ' . $version->version_created_at . ')</p>';
                            $version->delete();
                            $deletedCount++;
                        }
                        
                        $html .= '<p class="success">✅ Cleaned up Policy #' . $policy->id . '</p>';
                        
                    } catch (Exception $e) {
                        $error = 'Error processing Policy #' . $policy->id . ': ' . $e->getMessage();
                        $errors[] = $error;
                        $html .= '<p class="error">❌ ' . $error . '</p>';
                    }
                }
                
                $html .= '<h2>📊 Cleanup Results</h2>';
                $html .= '<div class="success">';
                $html .= '<p>✅ Versions deleted: <strong>' . $deletedCount . '</strong></p>';
                $html .= '<p>✅ Policies processed: <strong>' . $processedCount . '</strong></p>';
                $html .= '<p>❌ Errors encountered: <strong>' . count($errors) . '</strong></p>';
                $html .= '</div>';
                
                if (!empty($errors)) {
                    $html .= '<h3 class="error">❌ Errors:</h3>';
                    $html .= '<ul>';
                    foreach ($errors as $error) {
                        $html .= '<li class="error">' . $error . '</li>';
                    }
                    $html .= '</ul>';
                }
                
                // Verify cleanup
                $html .= '<h2>🔍 Verifying Cleanup...</h2>';
                $remainingDuplicates = Policy::with('versions')
                    ->get()
                    ->filter(function ($policy) {
                        return $policy->versions->count() > 1;
                    })
                    ->count();
                
                if ($remainingDuplicates === 0) {
                    $html .= '<p class="success">✅ Cleanup successful! No policies have duplicate versions.</p>';
                    $html .= '<p class="success">🎉 Your policy system is now clean!</p>';
                } else {
                    $html .= '<p class="warning">⚠️ Warning: ' . $remainingDuplicates . ' policies still have multiple versions.</p>';
                }
                
            } else {
                $html .= '<h2 class="warning">⚠️ Ready to Cleanup</h2>';
                $html .= '<div class="warning">';
                $html .= '<p><strong>This will permanently delete duplicate policy versions!</strong></p>';
                $html .= '<p>This script will:</p>';
                $html .= '<ul>';
                $html .= '<li>Keep only version 1 for each policy</li>';
                $html .= '<li>Delete all versions 2, 3, 4, etc.</li>';
                $html .= '<li>This action cannot be undone!</li>';
                $html .= '</ul>';
                $html .= '</div>';
                
                $html .= '<p><a href="?action=cleanup" class="btn">🗑️ DELETE DUPLICATE VERSIONS</a></p>';
                $html .= '<p><small>Click the button above to start the cleanup process</small></p>';
            }
        }
        
    } catch (Exception $e) {
        $html .= '<h2 class="error">❌ Error</h2>';
        $html .= '<p class="error">Failed to run cleanup script: ' . $e->getMessage() . '</p>';
        $html .= '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
    $html .= '</div>';
    $html .= '<p style="text-align: center; margin-top: 30px; color: #666;">Policy Version Cleanup Script - Insurance Management System</p>';
    
    return $html;
});
