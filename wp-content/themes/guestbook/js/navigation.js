(function() {
  function toggleFocus() {
    var self = this;
    for (;-1 === self.className.indexOf("nav-menu");) {
      if ("li" === self.tagName.toLowerCase()) {
        if (-1 !== self.className.indexOf("focus")) {
          self.className = self.className.replace(" focus", "");
        } else {
          self.className += " focus";
        }
      }
      self = self.parentElement;
    }
  }
  var container;
  var button;
  var menu;
  var links;
  var i$$1;
  var len;
  container = document.getElementById("site-navigation");
  if (!container) {
    return;
  }
  button = container.getElementsByTagName("button")[0];
  if ("undefined" === typeof button) {
    return;
  }
  menu = container.getElementsByTagName("ul")[0];
  if ("undefined" === typeof menu) {
    button.style.display = "none";
    return;
  }
  menu.setAttribute("aria-expanded", "false");
  if (-1 === menu.className.indexOf("nav-menu")) {
    menu.className += " nav-menu";
  }
  button.onclick = function() {
    if (-1 !== container.className.indexOf("toggled")) {
      container.className = container.className.replace(" toggled", "");
      button.setAttribute("aria-expanded", "false");
      menu.setAttribute("aria-expanded", "false");
    } else {
      container.className += " toggled";
      button.setAttribute("aria-expanded", "true");
      menu.setAttribute("aria-expanded", "true");
    }
  };
  links = menu.getElementsByTagName("a");
  i$$1 = 0;
  len = links.length;
  for (;i$$1 < len;i$$1++) {
    links[i$$1].addEventListener("focus", toggleFocus, true);
    links[i$$1].addEventListener("blur", toggleFocus, true);
  }
  (function(container) {
    var touchStartFn;
    var i$$0;
    var parentLink = container.querySelectorAll(".menu-item-has-children > a, .page_item_has_children > a");
    if ("ontouchstart" in window) {
      touchStartFn = function(e) {
        var menuItem = this.parentNode;
        var i;
        if (!menuItem.classList.contains("focus")) {
          e.preventDefault();
          i = 0;
          for (;i < menuItem.parentNode.children.length;++i) {
            if (menuItem === menuItem.parentNode.children[i]) {
              continue;
            }
            menuItem.parentNode.children[i].classList.remove("focus");
          }
          menuItem.classList.add("focus");
        } else {
          menuItem.classList.remove("focus");
        }
      };
      i$$0 = 0;
      for (;i$$0 < parentLink.length;++i$$0) {
        parentLink[i$$0].addEventListener("touchstart", touchStartFn, false);
      }
    }
  })(container);
})();