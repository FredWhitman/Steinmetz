# Add Production Log Modal Workflow

## 1. Overview  
Three logical sections in the modal:  
- **Log Information**: Part Name, Date, Run Status (Start / In Progress / End)  
- **Material Usage**: Hopper 1–4 lbs → Blender Total, Daily Usage, Percentages  
- **Dryer & Production**: Temps, Press Counter, Rejects, Purge  
- **Comments & Actions**: Free-text notes + Cancel/Add buttons  

## 2. Run Status Behavior  
- **Start (1)**  
  - Copy hopper values → daily usage fields  
  - Compute percentages & totals  
  - Validate no existing run for this product/date  

- **In Progress (0)**  
  - Fetch previous mat log → subtract from current  
  - Populate daily usage & percentages  

- **End (2)**  
  - Same as “In Progress”  
  - Roll up all logs in run → material totals, rejects, purge  
  - Update `production_run` record → `Completed = yes`  

## 3. Core Calculation Logic  
- `doPercentage(total, value)` → `(value/total)*100` with 2-decimal precision  
- `getAndSetDailyUsage(...)` → sets `dHop1–4`, `dTotal`, `dHop1–4p`, `dTotalp`  
- Optional hoppers (3 & 4) only if value > 0  

## 4. UI/UX & Validation  
- Loader uses `showLoader()` / `hideLoader()` + `requestAnimationFrame()` + 300 ms minimum display  
- Bootstrap validation on submit → `was-validated`  
- Alerts via `showAlertMessage(msg, containerID)`  
- Prevent unwanted page reloads on links/buttons  

## 5. Modular Refactor Blueprint  
- **productionApiClient.js** → all fetch/POST calls  
- **productionUiManager.js** → DOM updates, form reset, loader/alert  
- **productionMain.js** → event wiring, control flow  
- **PHP Layers**  
  - `prodDispatcher.php`, `productionController.php` → route AJAX actions  
  - `prodInit.php` → bootstrap, configuration  
  - `LogFactory.php` → map DB rows → objects  
  - `util.php` → shared helpers  
