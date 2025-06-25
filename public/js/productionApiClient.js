//productionApiClient.js

import { showLoader, hideLoader } from "./productionUiManager.js";

const BASE_URL = "/api/prodDispatcher.php";

// Fetch last4wks production log data (GET request)
export async function fetchProdLogs() {
  try {
    const response = await fetch("/api/prodDispatcher.php?read4wks=1");

    if (!response.ok) {
      console.error("API returned error:", response.statusText);
      return;
    }
    const jsonData = await response.json();
    console.log("parsed production log data: ", jsonData);

    return jsonData;
  } catch (error) {
    console.error("Error getting production logs: ", error);
  }
}

/* export async function fetchAndFillForm(id, table) {
  const url = `${BASE_URL}?view${capitalize(table)}=1&id=${id}&table=${table}`;
  console.log("📨 FetchFillForm URL:", url);

  try {
    const currentLog = await fetchAndParseJSON(url);
    if (!currentLog || currentLog.error) {
      console.error("❌ Current log error:", currentLog?.error);
      return;
    }

    fillFormFields(currentLog, fieldMappings[table]);

    if (table === "prodLogs") {
      const previousLog = await fetchPreviousLog(currentLog.prevProdLogID);
      const metrics = calculateDailyMetrics(currentLog, previousLog);

      fillDailyUsageFields(metrics.usage);
      fillPercentageFields(metrics.percentages);
    }
  } catch (error) {
    console.error("🔥 fetchAndFillForm failed:", error);
  }
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

async function fetchAndParseJSON(url) {
  const response = await fetch(url);
  const raw = await response.text();
  return JSON.parse(raw);
}

function fillFormFields(data, mapping) {
  Object.entries(mapping).forEach(([dbKey, formID]) => {
    const element = document.getElementById(formID);
    if (element) {
      element.value = data[dbKey] ?? "";
    } else {
      console.warn(`⚠️ Element not found: ${formID}`);
    }
  });
}

async function fetchPreviousLog(previousLogID) {
  if (!previousLogID) return {};
  const url = `${BASE_URL}?viewProdLogs=1&id=${previousLogID}&table=prodLogs`;
  console.log("🔁 Fetching previous log:", url);
  try {
    const log = await fetchAndParseJSON(url);
    if (log?.error) console.error("Previous log fetch error:", log.error);
    return log;
  } catch (err) {
    console.error("Failed to fetch previous log:", err);
    return {};
  }
}

function calculateDailyMetrics(current, previous = {}) {
  const parseOrZero = (v) => parseFloat(v) || 0;

  const usage = {
    mat1: parseOrZero(current.matUsed1) - parseOrZero(previous.matUsed1),
    mat2: parseOrZero(current.matUsed2) - parseOrZero(previous.matUsed2),
    mat3: parseOrZero(current.matUsed3) - parseOrZero(previous.matUsed3),
    mat4: parseOrZero(current.matUsed4) - parseOrZero(previous.matUsed4),
    press:
      parseOrZero(current.pressCounter) - parseOrZero(previous.pressCounter),
    blender:
      parseOrZero(current.blenderTotals) - parseOrZero(previous.blenderTotals),
    rejects: parseOrZero(current.startUpRejects),
  };

  const totalMat = usage.mat1 + usage.mat2 + usage.mat3 + usage.mat4;

  const percentages = {
    rejects: usage.press
      ? ((usage.rejects / usage.press) * 100).toFixed(2)
      : "0",
    mat1: totalMat ? ((usage.mat1 / totalMat) * 100).toFixed(2) : "0",
    mat2: totalMat ? ((usage.mat2 / totalMat) * 100).toFixed(2) : "0",
    mat3: totalMat ? ((usage.mat3 / totalMat) * 100).toFixed(2) : "0",
    mat4: totalMat ? ((usage.mat4 / totalMat) * 100).toFixed(2) : "0",
    blenderTotals: usage.press
      ? ((usage.blender / usage.press) * 100).toFixed(2)
      : "0",
  };

  return { usage, percentages };
}

function fillDailyUsageFields(usage) {
  document.getElementById("vDailyMat1Usage").value = usage.mat1.toFixed(2);
  document.getElementById("vDailyMat2Usage").value = usage.mat2.toFixed(2);
  document.getElementById("vDailyMat3Usage").value = usage.mat3.toFixed(2);
  document.getElementById("vDailyMat4Usage").value = usage.mat4.toFixed(2);
  document.getElementById("vDailyPressCounter").value = usage.press.toFixed(0);
  document.getElementById("vDailyBlenderTotals").value =
    usage.blender.toFixed(2);
}

function fillPercentageFields(p) {
  document.getElementById("vPercentRejects").value = `${p.rejects}%`;
  document.getElementById("vPercentMat1").value = `${p.mat1}%`;
  document.getElementById("vPercentMat2").value = `${p.mat2}%`;
  document.getElementById("vPercentMat3").value = `${p.mat3}%`;
  document.getElementById("vPercentMat4").value = `${p.mat4}%`;
  document.getElementById(
    "vPercentBlenderTotals"
  ).value = `${p.blenderTotals}%`;
} */
