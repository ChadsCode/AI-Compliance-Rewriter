<?php
// ajax/run-assistant.php
require_once __DIR__ . '/../../../toneanalysis-engine/config/config.php';
require_once __DIR__ . '/../../../toneanalysis-engine/core/agent-runner.php';
require_once __DIR__ . '/../../../toneanalysis-engine/core/request-processor.php';

// Get parameters
$assistant_path = $_POST['assistant'] ?? '';
$user_input = $_POST['input'] ?? '';
$role_input = $_POST['role'] ?? '';
$state = $_POST['state'] ?? '';

// Validate required fields
if (empty($assistant_path) || empty($user_input)) {
    echo "Missing required parameters";
    exit;
}

// Process request
try {
    $response = process_tone_request($assistant_path, $user_input, $role_input, $state);
    echo $response;
} catch (Exception $e) {
    echo "Processing error: " . $e->getMessage();
}