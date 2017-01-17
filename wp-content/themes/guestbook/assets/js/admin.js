jQuery(document).ready(function ($) {
  var formfield, imgurl;
  
  $('.file-upload-button ').click(function () {
    formfield = $(this).prev('.file-upload-text');
    tb_show('', 'media-upload.php?type=file&amp;TB_iframe=true');
    return false;
  });
  
  window.send_to_editor = function (html) {
    imgurl = $('img', html).attr('src');
    formfield.val(imgurl);
    tb_remove();
  };
});