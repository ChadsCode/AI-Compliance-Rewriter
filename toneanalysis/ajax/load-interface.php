<?php
// ajax/load-interface.php - Output JavaScript directly instead of including
header('Content-Type: application/javascript');

// Define the JavaScript functions directly here instead of including
echo <<<EOT
function initToneAnalysis() {
  const inputBox = document.getElementById("user-input");
  const sendButton = document.querySelector("button");
  const outputBox = document.getElementById("output");

  // Disable send button if input is empty
  inputBox.addEventListener("input", function() {
    sendButton.disabled = !this.value.trim();

    // Optional: Quietly enforce 400-word limit
    const wordLimit = 400;
    const currentWordCount = this.value.trim().split(/\\s+/).length;
    if (currentWordCount > wordLimit) {
      this.value = this.value.trim().split(/\\s+/).slice(0, wordLimit).join(" ");
    }
  });

  // Add Enter key support
  inputBox.addEventListener("keydown", function(e) {
    if (e.key === "Enter" && !e.shiftKey && this.value.trim()) {
      e.preventDefault();
      runAssistant();
    }
  });
}

function processToneRequest(input, assistantPath, role, state) {
  const outputBox = document.getElementById("output");
  outputBox.textContent = "Processing...";
  outputBox.classList.add("processing");

  fetch("ajax/run-assistant.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "input=" + encodeURIComponent(input) + 
          "&assistant=" + encodeURIComponent(assistantPath) +
          "&role=" + encodeURIComponent(role) +
          "&state=" + encodeURIComponent(state)
  })
  .then(response => response.text())
  .then(data => {
    outputBox.classList.remove("processing");
    outputBox.textContent = data;
  })
  .catch(error => {
    outputBox.classList.remove("processing");
    outputBox.textContent = "Error: " + error;
  });
}

// Execute initialization when the document is ready
document.addEventListener("DOMContentLoaded", function() {
  initToneAnalysis();
});
EOT;