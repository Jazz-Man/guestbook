var bsn = require('../module/bootstrap.native.js');
var $$ = require('domtastic');

var carouselBox = $$('#carousel123');

if (carouselBox.length) {
  var carousel = new bsn.Carousel(carouselBox[0], {
    interval: 2000,
  });
  var slides = $$(carousel.slides);
  
  if (slides.length) {
    slides.forEach(function (e) {
      var _this = $$(e);
      var itemToClone = _this;
      // console.log(nextEl(_this));
      
      for (var i = 1; i < 4; i++) {
        
        itemToClone = [_this.prop('nextElementSibling')];
        
        // console.log(_this.parent().prop('firstElementChild'));
        if (!itemToClone.length) {
          // console.log(itemToClone);
          itemToClone = _this.parent().prop('firstElementChild');
        }
        // console.log(itemToClone);
        
        // if (itemToClone[0].nextSibling !== null){
        //   itemToClone = [itemToClone[0].nextSibling];
        // }
        // if (!itemToClone.length) {
        //   // itemToClone = _this.siblings();
        //   // console.log(itemToClone);
        // }
        
      }
      
    });
  }
  
  // $('.carousel-showmanymoveone .item').each(function () {
  //   var _this = $(this);
  //   var itemToClone = _this;
  //
  //   for (var i = 1; i < 4; i++) {
  //     itemToClone = itemToClone.next();
  //
  //     if (!itemToClone.length) {
  //       itemToClone = _this.siblings(':first');
  //     }
  //
  //     itemToClone.children(':first-child').clone()
  //                .addClass("cloneditem-" + (i))
  //                .appendTo(_this);
  //   }
  // });
}