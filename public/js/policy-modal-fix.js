// Policy Modal Diagnostic and Fix Script
// Run this in browser console to diagnose issues

console.log('=== Policy Modal Diagnostic Script ===');

// Check if jQuery is loaded
console.log('1. jQuery loaded:', typeof $ !== 'undefined');

// Check if CSRF token exists
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
console.log('2. CSRF token present:', !!csrfToken);
if (csrfToken) {
    console.log('   Token preview:', csrfToken.substring(0, 20) + '...');
}

// Check if modal exists
console.log('3. Policy modal exists:', $('#policyModal').length > 0);

// Check if form elements exist
console.log('4. Form elements check:');
console.log('   - policyTypeSelect:', $('#policyTypeSelect').length > 0);
console.log('   - businessTypeSelect:', $('#businessTypeSelect').length > 0);
console.log('   - hiddenPolicyType:', $('#hiddenPolicyType').length > 0);
console.log('   - hiddenBusinessType:', $('#hiddenBusinessType').length > 0);
console.log('   - motorForm:', $('#motorForm').length > 0);
console.log('   - healthForm:', $('#healthForm').length > 0);
console.log('   - lifeForm:', $('#lifeForm').length > 0);

// Check current modal state
console.log('5. Modal state:');
console.log('   - Modal visible:', $('#policyModal').is(':visible'));
console.log('   - Step 1 visible:', $('#step1').is(':visible'));
console.log('   - Step 2 visible:', $('#step2').is(':visible'));
console.log('   - Step 3 visible:', $('#step3').is(':visible'));

// Check selected values
console.log('6. Current selections:');
console.log('   - policyTypeSelect value:', $('#policyTypeSelect').val());
console.log('   - businessTypeSelect value:', $('#businessTypeSelect').val());
console.log('   - hiddenPolicyType value:', $('#hiddenPolicyType').val());
console.log('   - hiddenBusinessType value:', $('#hiddenBusinessType').val());

// Check which form is active
console.log('7. Active form:');
console.log('   - motorForm active:', $('#motorForm').hasClass('active'));
console.log('   - healthForm active:', $('#healthForm').hasClass('active'));
console.log('   - lifeForm active:', $('#lifeForm').hasClass('active'));

// Check if API functions exist
console.log('8. API functions:');
console.log('   - apiCall exists:', typeof apiCall !== 'undefined');
console.log('   - createPolicyWithFiles exists:', typeof createPolicyWithFiles !== 'undefined');
console.log('   - handlePolicySubmit exists:', typeof handlePolicySubmit !== 'undefined');

// Test API endpoint
console.log('9. Testing API endpoint...');
fetch('/api/agents/list', {
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    }
})
.then(response => {
    console.log('   API test response status:', response.status);
    return response.json();
})
.then(data => {
    console.log('   API test successful, agents loaded:', data.agents?.length || 0);
})
.catch(error => {
    console.error('   API test failed:', error);
});

console.log('\n=== Diagnostic Script Complete ===');
console.log('If you see errors above, please share them for further diagnosis.');

