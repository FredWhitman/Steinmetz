Production Landing Page Overview
This document describes the architecture and flow of the production landing page in the Steinmetz project. It covers the main PHP and JavaScript files involved in handling production logs, material usage, and UI interactions.

Key Files
PHP Backend
ProductionModel.php

Contains the ProductionModel class, which handles all database operations related to production logs, material logs, inventory updates, and production runs.
Key methods:
read4wks(): Fetches the last four weeks of production logs.
insertProdLog(): Inserts a new production log, updates inventory, and handles transactions.
getProductList(), getMaterialList(): Retrieves lists of products and materials for UI select options.
Additional methods for updating inventory, checking for existing logs, and managing production runs.
prodInit.php

Initializes dependencies: autoload, model, controller, logger, and utility classes.
Returns an instance of the production controller for use in dispatchers.
prodDispatcher.php

Acts as the API endpoint for production-related requests.
Handles both GET and POST requests:
POST (e.g., addLog): Adds a new production log.
GET: Supports actions like reading logs, viewing details, checking for existing logs/runs, and retrieving product/material lists.
Routes requests to the appropriate controller methods.
JavaScript Frontend
productionApiClient.js

Provides functions to interact with the production API (prodDispatcher.php).
Handles fetching logs, product/material lists, checking for existing logs/runs, and posting new production logs.
Includes error handling and response parsing.
productionUiManager.js

Manages UI rendering and interactions for the production landing page.
Builds tables, populates select fields, handles alerts and loaders, and manages form field updates.
Calculates daily material usage and percentages for display.
productionMain.js

Initializes the landing page on load.
Wires up event listeners for table interactions, modal display, form submission, and input validation.
Coordinates fetching data, validating production runs/logs, and updating the UI after log submissions.
Data Flow
Page Load

JS initializes and fetches the last four weeks of production logs via fetchProdLogs().
Renders the logs table and sets up event listeners.
Viewing/Adding Logs

Selecting a log or opening the add modal triggers API calls to fetch product/material lists and previous log data.
UI fields are populated and validated.
Submitting a Log

Form data is validated and sent via postProductionLog() to the API.
The backend processes the log, updates inventory, and returns a success or error message.
The UI updates the table and displays feedback.
API Endpoints
GET /api/prodDispatcher.php?read4wks=1
GET /api/prodDispatcher.php?action=getProducts
GET /api/prodDispatcher.php?action=getMaterials
GET /api/prodDispatcher.php?action=checkIfLogExists&productID=...&date=...
GET /api/prodDispatcher.php?action=checkRun&productID=...
POST /api/prodDispatcher.php (with action: addLog)
Notes
All backend operations are logged for debugging and auditing.
Transactions ensure data integrity when inserting logs and updating inventory.
The frontend provides real-time feedback and validation to prevent duplicate logs or invalid production runs.
