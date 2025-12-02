// public file: script.js
// The initialization is now handled in load-interface.php
// Only define the runAssistant function here

function runAssistant() {
  const input = document.getElementById('user-input').value;
  const assistantPath = document.getElementById('assistant-selector').value;
  const role = document.getElementById('role').value;
  const state = document.getElementById('state-selector').value;
  
  processToneRequest(input, assistantPath, role, state);
}