const hamburger = document.querySelector(".mobile__hamburger");
const nav = document.querySelector("nav");
const body = document.querySelector("body");

hamburger.addEventListener("click", () => {
  nav.classList.toggle("open");
  body.classList.toggle("open");
});

const backToTop = document.querySelector("#backToTop");

window.addEventListener("scroll", () => {
  let scrollVertical = window.scrollY;

  if (scrollVertical > 300) {
    backToTop.classList.add("active");
  } else {
    backToTop.classList.remove("active");
  }
});

const choices = new Choices("#category-select", {
  removeItemButton: true,
});
