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
});
