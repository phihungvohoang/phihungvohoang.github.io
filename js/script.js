$(document).ready(function () {
  let loading = $(".blockLoading");
  let loadWeb = $(".blockLoadWeb");
  let loadContent = $(".blockContent");
  let loadBody = $("body");
  let loadHTML = $("html");
  let itemTab = $(".item__listChild--item");
  let itemCate = $(".blockMenu__listCate--item");
  loadWeb.addClass("active");
  loadBody.addClass("disabled");
  loadHTML.addClass("disabled");

  setTimeout(function () {
    loadWeb.removeClass("active");
  }, 2000);

  setTimeout(function () {
    loadContent.removeClass("disabled");
    loadBody.removeClass("disabled");
    loadHTML.removeClass("disabled");
  }, 2600);

  loadBody.click(function () {
    itemCate.removeClass("active");
  });

  itemCate.click(function (e) {
    e.stopPropagation();
    itemCate.removeClass("active");
    $(this).addClass("active");
  });

  itemTab.click(function (e) {
    e.stopPropagation();
    itemCate.removeClass("active");
    loading.addClass("active");
    $(".item__nameCate").removeClass("active");
    setTimeout(function () {
      loading.removeClass("active");
    }, 800);

    if (itemTab.hasClass("active")) {
      $(this)
        .parent()
        .parent()
        .children(".item__nameCate")
        .removeClass("active");
      $(this).parent().parent().children(".item__nameCate").addClass("active");
    } else {
      $(this)
        .parent()
        .parent()
        .children(".item__nameCate")
        .removeClass("active");
    }
  });
  $.getJSON("./json/database.json", function (data) {
    const categories = data.categories;
    categories.forEach((category) => {
      const categoryElement = $(`#${category.id}`);
      category.subcategories.forEach((subcategory) => {
        let productHtml = `
          <div class="listPage__item--title">
              <p class="title__text">${subcategory.name}</p>
          </div>    
          <div class="listPage__item--listProduct">`;
        subcategory.products.forEach((product) => {
          productHtml += `
                <div class="listProduct__item">
                    <div class="item__wrapImg">
                        <img class="item__wrapImg--img" src="${product.image}" />
                    </div>
                    <div class="item__wrapInfo">
                        <div class="item__wrapInfo--name">${product.name}</div>
                        <div class="item__wrapInfo--listPrice">
                            <div class="listPrice__item">
                                <p class="listPrice__item--info">${product.condition}</p>
                                <p class="listPrice__item--price">${product.price}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        productHtml += `</div>`;

        categoryElement.append(productHtml);
      });
    });
  });
});

function selectTab(event, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("listPage__item");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("item__listChild--item");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
    $("html, body").scrollTop(0);
  }
  document.getElementById(tabName).style.display = "block";
  event.currentTarget.className += " active";
}
