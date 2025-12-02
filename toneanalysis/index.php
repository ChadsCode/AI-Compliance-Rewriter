<!DOCTYPE html>
<html>
<head>
  <title>BETA Compliance Assistant</title>
  <link rel="stylesheet" href="style.css">
  <!-- Load only the interface JS with all required functions -->
  <script src="ajax/load-interface.php"></script>
  <!-- Then load the main script -->
  <script src="script.js"></script>
</head>
<body>
  <h2>Compliance Assistant</h2>

  <input type="text" id="role" placeholder="Enter your role (e.g., Nurse, Risk Manager)">
  
  <div class="location-selectors">
    <select id="state-selector">
      <option value="">Select State (Optional)</option>
      <option value="alabama">Alabama</option>
      <option value="alaska">Alaska</option>
      <option value="arizona">Arizona</option>
      <option value="arkansas">Arkansas</option>
      <option value="california">California</option>
      <option value="colorado">Colorado</option>
      <option value="connecticut">Connecticut</option>
      <option value="delaware">Delaware</option>
      <option value="florida">Florida</option>
      <option value="georgia">Georgia</option>
      <option value="hawaii">Hawaii</option>
      <option value="idaho">Idaho</option>
      <option value="illinois">Illinois</option>
      <option value="indiana">Indiana</option>
      <option value="iowa">Iowa</option>
      <option value="kansas">Kansas</option>
      <option value="kentucky">Kentucky</option>
      <option value="louisiana">Louisiana</option>
      <option value="maine">Maine</option>
      <option value="maryland">Maryland</option>
      <option value="massachusetts">Massachusetts</option>
      <option value="michigan">Michigan</option>
      <option value="minnesota">Minnesota</option>
      <option value="mississippi">Mississippi</option>
      <option value="missouri">Missouri</option>
      <option value="montana">Montana</option>
      <option value="nebraska">Nebraska</option>
      <option value="nevada">Nevada</option>
      <option value="new-hampshire">New Hampshire</option>
      <option value="new-jersey">New Jersey</option>
      <option value="new-mexico">New Mexico</option>
      <option value="new-york">New York</option>
      <option value="north-carolina">North Carolina</option>
      <option value="north-dakota">North Dakota</option>
      <option value="ohio">Ohio</option>
      <option value="oklahoma">Oklahoma</option>
      <option value="oregon">Oregon</option>
      <option value="pennsylvania">Pennsylvania</option>
      <option value="rhode-island">Rhode Island</option>
      <option value="south-carolina">South Carolina</option>
      <option value="south-dakota">South Dakota</option>
      <option value="tennessee">Tennessee</option>
      <option value="texas">Texas</option>
      <option value="utah">Utah</option>
      <option value="vermont">Vermont</option>
      <option value="virginia">Virginia</option>
      <option value="washington">Washington</option>
      <option value="west-virginia">West Virginia</option>
      <option value="wisconsin">Wisconsin</option>
      <option value="wyoming">Wyoming</option>
      <option value="district-of-columbia">District of Columbia</option>
      <option value="federal">Federal (All States)</option>
    </select>
  </div>
  
  <select id="assistant-selector">
    <!-- Financial Services -->
    <option value="financial-services/financial-services-assistant.json">Financial Services</option>

    <!-- General Business -->
    <option value="general-business/general-business-assistant.json">General Business</option>

    <!-- Government -->
    <option value="government/government-assistant.json">Government</option>

    <!-- Healthcare -->
    <option value="healthcare/healthcare-assistant.json">Healthcare</option>

    <!-- Human Resources -->
    <option value="human-resources/hr-assistant.json">Human Resources</option>

    <!-- Legal Services -->
    <option value="legal-services/legal-services-assistant.json">Legal Services</option>
  </select>

  <textarea id="user-input" placeholder="Enter your message here..."></textarea>
  <button onclick="runAssistant()">Send</button>
  <pre id="output"></pre>
</body>
</html>