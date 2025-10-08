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

// Function to populate table
function populateTable(data) {
  const tableBody = document.getElementById("table-body");
  tableBody.innerHTML = ""; // Clear existing content

  if (data.length === 0) {
    // Show a "No records" message if data is empty
    const noRecordsRow = document.createElement("tr");
    noRecordsRow.innerHTML = `<td colspan="5" class="text-center">No records found</td>`;
    tableBody.appendChild(noRecordsRow);
  } else {
    // Populate table rows with data
    data.forEach((item) => {
      const row = document.createElement("tr");

      row.innerHTML = `
              <td>${item.id}</td>
              <td>${item.name}</td>
              <td>${item.case}</td>
              <td>${item.year}</td>
              <td><img src="${item.image}" alt="Image" class="img-thumbnail"></td>
          `;

      tableBody.appendChild(row);
    });
  }
}

// Function to filter data by year
function filterDataByYear(data, year) {
  return data.filter((item) => item.year === parseInt(year, 10));
}

// Main logic
document.addEventListener("DOMContentLoaded", async () => {
  const year = getQueryParam("year");
  const jsonData = await fetchJSONData("./json/sth.json");
  const filteredData = year ? filterDataByYear(jsonData, year) : jsonData;
  populateTable(filteredData);
});
