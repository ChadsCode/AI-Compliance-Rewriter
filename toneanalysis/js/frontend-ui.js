document.addEventListener("DOMContentLoaded", function () {
  const inputBox = document.getElementById("user-input");
  const sendButton = document.querySelector("button");
  const outputBox = document.getElementById("output");

  // Disable send button if input is empty
  inputBox.addEventListener("input", function () {
    sendButton.disabled = !this.value.trim();

    // Optional: Quietly enforce 400-word limit
    const wordLimit = 400;
    const currentWordCount = this.value.trim().split(/\s+/).length;
    if (currentWordCount > wordLimit) {
      this.value = this.value.trim().split(/\s+/).slice(0, wordLimit).join(" ");
    }
  });

  // Show loading message before fetch starts
  window.showLoading = function () {
    outputBox.textContent = "Running assistant...";
  };

  // Show final result and scroll into view
  window.showResult = function (result) {
    outputBox.textContent = result;
    outputBox.scrollIntoView({ behavior: "smooth" });
  };
});