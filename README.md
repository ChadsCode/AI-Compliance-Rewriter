# AI-Compliance-Rewriter

Role-based compliance message rewriter for regulated industries. Modular assistant configurations with hierarchical federal and state regulation support.

Repository: https://github.com/ChadsCode/AI-Compliance-Rewriter

## What This Is

A message rewriting tool that takes unprofessional or risky language and rewrites it for regulatory compliance. Select your industry, role, and state. Input a message. Receive a compliant rewrite with cited sources.

Each industry has its own assistant configuration with specific tone profiles, compliance rules, and regulatory documents. State selection adds jurisdiction-specific context with precedence guidance for federal/state conflicts.

## Why It Matters

If you're in a regulated industry (finance, healthcare, government, legal, HR), written communications are discoverable. Tone matters. Phrasing matters. A frustrated message in a medical record or client file becomes a liability.

This tool catches problematic tone before it becomes a compliance issue. The rewrite maintains the original intent while applying industry-appropriate language and referencing applicable regulations.

## Demo

- Demo 1: https://www.linkedin.com/feed/update/urn:li:activity:7333104104010907651/
- Demo 2: https://www.linkedin.com/feed/update/urn:li:activity:7401399167253929984/

## Supported Industries

- Financial Services (SEC, FINRA, GLBA, Regulation Best Interest)
- General Business (FTC Act, CCPA, Copyright Law)
- Government (FOIA, Privacy Act, Public Records)
- Healthcare (HIPAA, HITECH, state privacy laws)
- Human Resources (ADA, ADEA, Title VII, FEHA)
- Legal Services (ABA Model Rules, Attorney-Client Privilege)

## State Support

Currently configured:
- Washington (Healthcare only - fully functional with precedence guidance)

Placeholder folders exist for California and New York but are not fully configured. Many industries include IMPROVE folders with development ideas for future expansion.

Adding a new state means adding a folder with the relevant regulation text files and precedence guidance.

## Project Structure

```
toneanalysis/
├── ajax/
│   ├── load-interface.php
│   └── run-assistant.php
├── js/
│   └── frontend-ui.js
├── index.php
├── script.js
└── style.css

toneanalysis-engine/
├── assistants/
│   ├── financial-services/
│   │   ├── financial-services-assistant.json
│   │   └── IMPROVE - FINANCIAL SERVICES ASSISTANT/
│   ├── general-business/
│   ├── government/
│   ├── healthcare/
│   ├── human-resources/
│   └── legal-services/
├── config/
│   └── config.php
├── core/
│   ├── agent-runner.php
│   ├── openai.php
│   └── request-processor.php
├── js/
│   └── tone-interface.php
├── regulations/
│   ├── financial-services/
│   │   ├── federal/
│   │   ├── california/
│   │   ├── new-york/
│   │   └── sources.txt
│   ├── general-business/
│   ├── government/
│   ├── healthcare/
│   ├── human-resources/
│   └── legal-services/
├── LICENSE
└── README.md
```

## How It Works

1. User selects industry, role, and state
2. System loads the corresponding assistant configuration (JSON)
3. Federal regulations are loaded first
4. State regulations are layered with precedence guidance
5. Message is analyzed and rewritten using OpenAI API
6. Output includes compliant rewrite plus sources consulted

## Adding a New Industry

1. Create a new folder in `/assistants/` with a JSON config file
2. Create a matching folder in `/regulations/` with federal and state subfolders
3. Add regulation text files and sources.txt
4. Add the option to the UI dropdown

## Adding New Regulations

Drop a `.txt` file into the appropriate `/regulations/[industry]/[jurisdiction]/` folder. The system loads all text files in the directory automatically.

## Technical Details

- Backend: PHP
- Frontend: Vanilla JavaScript
- API: OpenAI (GPT-4o)
- Architecture: Modular assistant configs, hierarchical regulation loading
- No framework dependencies

## Requirements

- PHP 7.4+
- OpenAI API key
- Web server (Apache/Nginx)

## Configuration

1. Copy `config/config.php` and add your OpenAI API key
2. Ensure the `/assistants/` and `/regulations/` directories are readable
3. Point your web server to the `/toneanalysis/` directory

**Important:** Never commit your API key to version control.

## Use Cases

- Healthcare: Chart notes, patient communications, care coordination
- Financial Services: Client communications, disclosures, advisory messages
- HR: Performance documentation, policy communications, accommodation responses
- Legal: Client correspondence, internal memos, case documentation
- Government: Public responses, FOIA replies, policy communications

## Limitations

- Prototype status, not production-ready
- Regulation text files are samples, not complete legal references
- State coverage limited to Washington (Healthcare only)
- Other state folders are placeholders
- Requires OpenAI API (not air-gapped)

## License

MIT License - Free for commercial and personal use

## Author

Chad Wigington  
LinkedIn: https://www.linkedin.com/in/chadwigington/  
GitHub: https://github.com/ChadsCode

## Disclosures

1. Personal hobby project created prior to employment.
2. Not associated with or endorsed by any employer.
3. This project is independently developed and is not affiliated with, endorsed by, or sponsored by OpenAI.
4. OpenAI, GPT, and GPT-4 are trademarks of OpenAI, Inc. Use of these names is for identification purposes only and does not imply endorsement, sponsorship, or affiliation. All trademarks remain the property of their respective owners.
5. Have all code professionally verified before use.
6. Views are my own.

---

Questions? Open an issue or reach out via LinkedIn: https://www.linkedin.com/in/chadwigington/
