// Function to get query parameters
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

// Function to fetch JSON data from a file
async function fetchJSONData(url) {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return await response.json();
  } catch (error) {
    console.error("Fetch error:", error);
    return [];
  }
}

// Function to populate a table
function populateTable(data, tableId) {
  const tableBody = document.getElementById(tableId);
  tableBody.innerHTML = ""; // Clear existing content

  if (data.length === 0) {
    // Show a "No records" message if data is empty
    const noRecordsRow = document.createElement("tr");
    noRecordsRow.innerHTML = `<td colspan="4" class="text-center">No records found</td>`;
    tableBody.appendChild(noRecordsRow);
  } else {
    // Populate table rows with data
    data.forEach((item) => {
      const row = document.createElement("tr");

      row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.case}</td>
            <td>${item.year}</td>
            <td><img src="${item.image}" alt="Image" class="img-thumbnail"></td>
        `;

      tableBody.appendChild(row);
    });
  }
}

// Function to filter data by year and type
function filterData(data, year, type) {
  return data.filter(
    (item) => item.year === parseInt(year, 10) && item.type === type
  );
}

// Main logic
document.addEventListener("DOMContentLoaded", async () => {
  const year = getQueryParam("year");
  const jsonData = await fetchJSONData("./json/th.json");

  // Filter data for Treasure Hunt (TH) and Super Treasure Hunt (STH)
  const thData = year
    ? filterData(jsonData, year, "th")
    : jsonData.filter((item) => item.type === "th");
  const sthData = year
    ? filterData(jsonData, year, "sth")
    : jsonData.filter((item) => item.type === "sth");

  // Populate tables
  populateTable(thData, "table-th-body");
  populateTable(sthData, "table-sth-body");
});
