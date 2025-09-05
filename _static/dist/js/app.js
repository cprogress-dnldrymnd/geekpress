const hamburger = document.querySelector(".mobile__hamburger");
const nav = document.querySelector("nav");

hamburger.addEventListener("click", () => {
  nav.classList.toggle("open");
});
