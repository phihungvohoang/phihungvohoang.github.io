window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  var scrollButton = document.querySelector(".scroll_top");
  if (
    document.body.scrollTop > 300 ||
    document.documentElement.scrollTop > 300
  ) {
    scrollButton.style.display = "block";
  } else {
    scrollButton.style.display = "none";
  }
}
document.addEventListener("DOMContentLoaded", function () {
  const carContainer = document.getElementById("carContainer");
  const searchInput = document.getElementById("searchInput");
  const conditionFilter = document.getElementById("conditionFilter");
  const priceFilter = document.getElementById("priceFilter");
  const tagFilter = document.getElementById("tagFilter");
  const sortBySelect = document.getElementById("sortBy");
  const noProductsMessage = document.createElement("div"); // Create a container for the message
  noProductsMessage.className = "alert alert-warning d-none"; // Initially hidden
  noProductsMessage.textContent = "No products found";

  let allCars = [];
  let selectedTags = [];
  let selectedType = null;

  // Fetch data from JSON file
  fetch("./json/cars2.json")
    .then((response) => response.json())
    .then((data) => {
      allCars = data;
      displayTags(data);
      displayCars(data);

      // Add event listeners for filtering
      searchInput.addEventListener("input", filterAndDisplayCars);
      conditionFilter.addEventListener("change", filterAndDisplayCars);
      priceFilter.addEventListener("change", filterAndDisplayCars);
      // Add event listener for sorting
      sortBySelect.addEventListener("change", filterAndDisplayCars);
      // Add event listener for tag filtering
      tagFilter.addEventListener("change", function () {
        const selectedTag = tagFilter.value;
        selectedTags = selectedTag ? [selectedTag] : [];
        filterAndDisplayCars();
      });

      // Handle clicks on new list items
      document
        .querySelector('a[href="#basic"]')
        .addEventListener("click", function (event) {
          event.preventDefault();
          selectedType = "basic";
          filterAndDisplayCars();
        });
      document
        .querySelector('a[href="#th"]')
        .addEventListener("click", function (event) {
          event.preventDefault();
          selectedType = "th";
          filterAndDisplayCars();
        });
      document
        .querySelector('a[href="#sth"]')
        .addEventListener("click", function (event) {
          event.preventDefault();
          selectedType = "sth";
          filterAndDisplayCars();
        });
      sortBySelect.value = "newest"; // Default sort option
      filterAndDisplayCars();
    })
    .catch((error) => console.error("Error fetching car data:", error));

  // Function to display cars
  function displayCars(cars) {
    carContainer.innerHTML = "";
    if (cars.length === 0) {
      carContainer.appendChild(noProductsMessage);
      noProductsMessage.classList.remove("d-none");
    } else {
      noProductsMessage.classList.add("d-none");
      cars.forEach((car) => {
        const card = document.createElement("div");
        card.className = "col-lg-4 col-xl-3 col-md-6 col-12 pt-2";
        card.innerHTML = `
          <div class="card">
            <div class="img-hover-zoom--quick-zoom">
              <img src="${car.image}" class="card-img-top" alt="${car.name}">
            </div>
            <div class="card-body">
                <h5 class="card-title">${car.name}</h5>
                <p class="card-text price ${car.condition}">${car.price}</p>
              <div class="card-details">
                <p class="card-text condition  ${car.condition}">${car.condition}</p>
                <p class="card-text stock">Stock: ${car.stock}</p>
              </div>
            </div>
          </div>
        `;
        carContainer.appendChild(card);
      });
    }
  }
  function displayTags(cars) {
    const tags = new Set();
    cars.forEach((car) => car.tags.forEach((tag) => tags.add(tag)));

    tagFilter.innerHTML =
      `<option value="">Filter by Tag</option>` +
      Array.from(tags)
        .map(
          (tag) => `
            <option value="${tag}">${tag}</option>
        `
        )
        .join("");
  }

  function filterAndDisplayCars() {
    const query = searchInput.value.toLowerCase();
    const condition = conditionFilter.value;
    const priceRange = priceFilter.value;
    const sortBy = sortBySelect.value; // Get the selected sorting option
    const [minPrice, maxPrice] = priceRange
      ? priceRange.split("-").map(Number)
      : [0, Infinity];

    const filteredCars = allCars.filter((car) => {
      const carPrice = parseInt(car.price.replace("đ", ""), 10);
      const matchesSearch = car.name.toLowerCase().includes(query);
      const matchesCondition = !condition || car.condition === condition;
      const matchesPrice = carPrice >= minPrice && carPrice <= maxPrice;
      const matchesTags =
        selectedTags.length === 0 ||
        selectedTags.some((tag) => car.tags.includes(tag));
      const matchesType = !selectedType || car.type === selectedType;

      return (
        matchesSearch &&
        matchesCondition &&
        matchesPrice &&
        matchesTags &&
        matchesType
      );
    });
    const sortedCars = filteredCars.sort((a, b) => {
      if (sortBy === "name-asc") {
        return a.name.localeCompare(b.name);
      } else if (sortBy === "name-desc") {
        return b.name.localeCompare(a.name);
      } else if (sortBy === "price-asc") {
        return (
          parseInt(a.price.replace("đ", ""), 10) -
          parseInt(b.price.replace("đ", ""), 10)
        );
      } else if (sortBy === "price-desc") {
        return (
          parseInt(b.price.replace("đ", ""), 10) -
          parseInt(a.price.replace("đ", ""), 10)
        );
      } else if (sortBy === "newest") {
        return b.id - a.id;
      } else if (sortBy === "oldest") {
        return a.id - b.id;
      } else {
        return 0; // No sorting
      }
    });

    displayCars(sortedCars);
  }
});
