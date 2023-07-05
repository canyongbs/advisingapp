/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

/* Sidebar - Side navigation menu on mobile/responsive mode */
window.toggleNavbar = function (collapseID) {
  document.getElementById(collapseID).classList.toggle('hidden')
  document.getElementById(collapseID).classList.toggle('bg-white')
  document.getElementById(collapseID).classList.toggle('m-2')
  document.getElementById(collapseID).classList.toggle('py-3')
  document.getElementById(collapseID).classList.toggle('px-6')
}

/* Opens sidebar navigation that contains sub-items */
window.openSubNav = function (el) {
  el.nextElementSibling.classList.toggle('hidden')
}

window.initialSubNavLoad = function () {
  document.getElementsByClassName('has-sub sidebar-nav-active').forEach(function(el) {
    window.openSubNav(el)
  })
}

/* Opens sidebar navigation that contains sub-items */
initialSubNavLoad()

/* Function for dropdowns */
window.openDropdown = function openDropdown(event, dropdownID) {
  let element = event.target;
  while (element.nodeName !== "A") {
    element = element.parentNode;
  }
  Popper.createPopper(element, document.getElementById(dropdownID), {
    placement: "bottom-start",
  });
  document.getElementById(dropdownID).classList.toggle("hidden");
  document.getElementById(dropdownID).classList.toggle("block");

  if (dropdownID == 'nav-notification-dropdown') {
    fetch('/admin/user-alerts/seen')
  }
}
