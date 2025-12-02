<?php
// core/agent-runner.php (modified version with sources footer)

require_once __DIR__ . '/openai.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Process a user message with the Chat Completions API
 * Includes hierarchical location-specific regulatory documents and sources footer
 *
 * @param string $user_message   The user's text
 * @param array $assistant_config The assistant configuration from JSON with location data
 * @return string                The processed text with sources footer
 * @throws Exception             If any API call fails
 */
function run_agent(string $user_message, array $assistant_config): string 
{
    // Extract configuration information
    $instructions = $assistant_config['instructions'] ?? '';
    $temperature = $assistant_config['temperature'] ?? 0.3;
    $model = "gpt-4o"; 
    $state = $assistant_config['state'] ?? '';
    $industry = '';
    
    if (isset($assistant_config['industry'])) {
        $industry = $assistant_config['industry'];
    } else if (isset($assistant_config['name'])) {
        $name_parts = explode(' ', $assistant_config['name']);
        $industry = strtolower(preg_replace('/[^a-z0-9]/', '-', $name_parts[0]));
    }
    
    // Track sources for footer
    $sources_used = [];
    
    // Look for regulatory documents based on hierarchy
    $regulatory_content = '';
    $precedence_guide = '';
    
    // Federal regulations first
    $federal_dir = __DIR__ . "/../regulations/{$industry}/federal";
    if (is_dir($federal_dir)) {
        $federal_content = '';
        foreach (glob($federal_dir . '/*.txt') as $regulation_file) {
            $file_name = basename($regulation_file);
            $content = file_get_contents($regulation_file);
            if (strlen($content) > 8000) {
                $content = substr($content, 0, 8000) . "... [content truncated]";
            }
            $federal_content .= "--- FEDERAL REGULATION: {$file_name} ---\n{$content}\n\n";
            
            // Add to sources
            $source_name = str_replace('.txt', '', $file_name);
            $sources_used['federal'][] = $source_name;
        }
        $regulatory_content .= $federal_content;
    }
    
    // State-level regulations second
    if (!empty($state) && $state !== 'federal') {
        $state_content = '';
        $state_dir = __DIR__ . "/../regulations/{$industry}/{$state}";
        if (is_dir($state_dir)) {
            foreach (glob($state_dir . '/*.txt') as $regulation_file) {
                $file_name = basename($regulation_file);
                $content = file_get_contents($regulation_file);
                if (strlen($content) > 5000) {
                    $content = substr($content, 0, 5000) . "... [content truncated]";
                }
                $state_content .= "--- {$state} STATE REGULATION: {$file_name} ---\n{$content}\n\n";
                
                // Add to sources
                $source_name = str_replace('.txt', '', $file_name);
                $sources_used['state'][] = $source_name;
            }
            $regulatory_content .= $state_content;
        }
        
        // Check for state-federal conflict file
        $conflict_file = __DIR__ . "/../regulations/{$industry}/{$state}/precedence.txt";
        if (file_exists($conflict_file)) {
            $precedence_guide = file_get_contents($conflict_file);
            $sources_used['precedence'][] = "{$state}_precedence_guide";
        } else {
            // Default precedence guide
            $precedence_guide = "LEGAL PRECEDENCE GUIDANCE:\n";
            $precedence_guide .= "1. Federal law generally supersedes state law under the Supremacy Clause when there is direct conflict.\n";
            $precedence_guide .= "2. State laws may provide additional protections beyond federal minimums.\n";
            $precedence_guide .= "3. In areas where federal law is silent or delegates authority to states, state laws control.\n";
            $precedence_guide .= "4. For post-Dobbs abortion regulations, state laws primarily govern access and restrictions as federal protections were removed.\n";
            $sources_used['precedence'][] = "default_precedence_guide";
        }
    }
    
    // Add precedence guide to regulatory content
    if (!empty($precedence_guide)) {
        $regulatory_content = $precedence_guide . "\n\n" . $regulatory_content;
    }
    
    // Check for sources file
    $sources_file = __DIR__ . "/../regulations/{$industry}/sources.txt";
    $sources_content = '';
    if (file_exists($sources_file)) {
        $sources_content = file_get_contents($sources_file);
        $sources_used['citations'] = "industry_specific_sources";
    }
    
    // Industry-specific sources
    $industry_sources_file = __DIR__ . "/../regulations/{$industry}/{$industry}_sources.txt";
    if (file_exists($industry_sources_file)) {
        $industry_sources = file_get_contents($industry_sources_file);
        $sources_content .= "\n" . $industry_sources;
        $sources_used['citations'] = "{$industry}_sources";
    }
    
    // State-specific sources
    if (!empty($state) && $state !== 'federal') {
        $state_sources_file = __DIR__ . "/../regulations/{$industry}/{$state}/{$state}_sources.txt";
        if (file_exists($state_sources_file)) {
            $state_sources = file_get_contents($state_sources_file);
            $sources_content .= "\n" . $state_sources;
            $sources_used['citations'] = "{$state}_sources";
        }
    }
    
    // Build context-aware system message
    $system_message = $instructions;
    if (!empty($regulatory_content)) {
        $system_message .= "\n\nREFERENCE THE FOLLOWING LOCATION-SPECIFIC REGULATIONS WHEN ANALYZING MESSAGES. FOLLOW THE PRECEDENCE GUIDANCE TO DETERMINE WHICH LAWS APPLY WHEN CONFLICTS EXIST:\n\n" . $regulatory_content;
    }
    
    if (!empty($sources_content)) {
        $system_message .= "\n\nSOURCES REFERENCE INFORMATION:\n" . $sources_content;
    }
    
    // Add requirement to include sources in response
    $system_message .= "\n\nIMPORTANT: You MUST end your response with a clearly formatted sources section that lists the regulations and legal principles applied in your analysis. Format it like this:

SOURCES CONSULTED:
[List relevant federal and state regulations referenced]

DISCLAIMER: This message has been reviewed for compliance with applicable regulations. Laws change frequently - please consult legal counsel for the most current guidance.";
    
    // Prepare messages array for chat completions
    $messages = [
        [
            'role' => 'system',
            'content' => $system_message
        ],
        [
            'role' => 'user',
            'content' => "ANALYSIS MODE: Review the following message according to your specific instructions and the provided regulations. Apply your industry-specific tone profile, censorship rules, and compliance requirements.\n\n" .
                         "MESSAGE TO ANALYZE: {$user_message}\n\n" .
                         "Return the corrected version of the message with appropriate tone adjustments and censorship applied. End your response with the required sources section."
        ]
    ];
    
    // Make the Chat Completions API call
    $response = openai_api('/v1/chat/completions', [
        'model' => $model,
        'messages' => $messages,
        'temperature' => $temperature,
        'max_tokens' => 2000,
    ]);
    
    if (empty($response['choices'][0]['message']['content'])) {
        throw new Exception("Empty response from API");
    }
    
    // Extract the response content
    $reply = $response['choices'][0]['message']['content'];
    
    // If no sources section was included by the model, add our own
    if (strpos(strtolower($reply), 'sources consulted') === false) {
        $reply .= "\n\n";
        $reply .= "SOURCES CONSULTED:\n";
        
        // Add federal sources if any
        if (!empty($sources_used['federal'])) {
            $reply .= "Federal: " . implode(', ', $sources_used['federal']) . "\n";
        } else {
            $reply .= "Federal: General regulatory principles\n";
        }
        
        // Add state sources if any
        if (!empty($sources_used['state'])) {
            $reply .= "State ({$state}): " . implode(', ', $sources_used['state']) . "\n";
        }
        
        // Add precedence information
        if (!empty($sources_used['precedence'])) {
            $reply .= "Legal Precedence: " . implode(', ', $sources_used['precedence']) . "\n";
        }
        
        // Add disclaimer
        $reply .= "\nDISCLAIMER: This message has been reviewed for compliance with applicable regulations as of " . date('F j, Y') . ". Laws change frequently - please consult legal counsel for the most current guidance.";
    }
    
    return $reply;
}

/**
 * Compatibility wrapper for backward compatibility
 */
function run_assistant(string $user_message, string $assistant_id): string
{
    // Try to load assistant configuration from JSON files
    $base_dir = __DIR__ . '/../assistants';
    
    // Try to find the matching assistant configuration
    $config = null;
    foreach (glob($base_dir . '/*/*.json') as $file) {
        $assistant_data = json_decode(file_get_contents($file), true);
        if (isset($assistant_data['id']) && $assistant_data['id'] === $assistant_id) {
            $config = $assistant_data;
            break;
        }
    }
    
    if (!$config) {
        throw new Exception("Assistant configuration not found for ID: $assistant_id");
    }
    
    // Run through the direct API
    return run_agent($user_message, $config);
}