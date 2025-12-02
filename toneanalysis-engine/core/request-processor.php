<?php
function process_tone_request($assistant_path, $user_input, $role_input, $state) {
    // Extract industry from path
    $industry = '';
    if (preg_match('/^([^\/]+)\//', $assistant_path, $matches)) {
        $industry = $matches[1];
    }
    
    // Format input with context
    $context = "[Industry: " . ucfirst(str_replace('-', ' ', $industry)) . "]\n";
    if (!empty($role_input)) {
        $context .= "[Role: {$role_input}]\n";
    }
    $context .= "[Country: United States]\n";
    if (!empty($state)) {
        $context .= "[State: " . ucfirst(str_replace('-', ' ', $state)) . "]\n";
    }
    $formatted_input = $context . "\n" . $user_input;
    
    // Load assistant configuration
    $base_dir = realpath(__DIR__ . '/../assistants');
    if (!$base_dir) {
        return "Error: Cannot resolve assistants directory path.";
    }
    
    // Validate path
    $normalized_path = str_replace('\\', '/', $assistant_path);
    $normalized_path = ltrim($normalized_path, '/');
    $full_path = $base_dir . '/' . $normalized_path;
    
    if (!file_exists($full_path)) {
        return "Error: Assistant configuration not found.";
    }
    
    // Load and process
    $config = json_decode(file_get_contents($full_path), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Error: Failed to parse assistant configuration.";
    }
    
    $config['country'] = 'united-states';
    $config['state'] = $state;
    
    return run_agent($formatted_input, $config);
}