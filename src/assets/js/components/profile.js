var $$ = require('domtastic');
var reqwest = require('reqwest');
var serialize = require('form-serialize');
var template = require('../module/template');
var bsn = require('../module/bootstrap.native.js');

// var ajaxurl = guestbook_params.ajaxurl;

var modalFrame = document.getElementById('modalFrame');
var deleteCommentAction = $$('.my-items .edit-options .delete');
var editCommentAction = $$('.my-items .edit-options .edit');

var addComments = $$('#add_comments');

if (deleteCommentAction.length) {

  deleteCommentAction.forEach(function (item) {
    var _this = $$(item);
    var href = _this.attr('href');
    var id = href.replace(/^.*c=(\d+).*$/, '$1');
    _this.on('click', function (e) {
      var commentContent = $$('#my-comment-' + id);
      var modalContent = template('modal-comment-delet', {
        content: commentContent.text(),
        href: href
      });
      var modal = new bsn.Modal(modalFrame, {
        content: modalContent
      });
      var deletBtn = $$(modal.modal).find('.delete');
      
      e.preventDefault();
      modal.open();

      deletBtn.on('click', function (e) {
        reqwest({
          url: guestbook_params.ajaxurl,
          data: {
            action: 'delete_comments_form',
            cid: id,
          },
          success: function (resp) {
            modal.content('Ok:)');
          },
          error: function (err) {
            console.log(err);
          }
        });
      });
    });
  });
}

if (editCommentAction.length) {
  editCommentAction.forEach(function (item) {
    var _this = $$(item);
    var href = _this.attr('href');
    var id = href.replace(/^.*c=(\d+).*$/, '$1');
    
    _this.on('click', function (e) {
      e.preventDefault();

      reqwest({
        url: guestbook_params.ajaxurl,
        method: 'post',
        type: 'json',
        data: {
          action: 'edit_comments_form',
          cid: id,
        },
        success: function (resp) {
          var modalContent = resp.data;
          var modal = new bsn.Modal(modalFrame, {
            content: modalContent
          });
          var formComment = $$(modal.modal).find('#gb_form');
  
          var formSave = $$(formComment).find('#gb-form-save');
          
          modal.open();
          

          formSave.on('click', function (e) {
            var formData = serialize(formComment[0], {
              hash: true,
              empty: true
            });
            e.preventDefault();

            reqwest({
              url: ajaxurl,
              method: 'post',
              type: 'json',
              data: {
                action: 'update_comment',
                data: formData
              },
              success: function (resp) {
                modal.content(resp.data);

                if (resp.success) {
                  window.location.reload();
                }
              }
            })
          });

        },
        error: function (err) {
          console.log(err);
        }
      });
    });
  });
}

if (addComments.length) {
  addComments.on('click', function (e) {
    e.preventDefault();

    reqwest({
      url: guestbook_params.ajaxurl,
      method: 'post',
      type: 'json',
      data: {
        action: 'add_comments_form',
      },
      success: function (resp) {
        var modalContent = resp.data;
        var modal = new bsn.Modal(modalFrame, {
          content: modalContent
        });
        var formComment = $$(modal.modal).find('#gb_form');
        var formSave = $$(formComment).find('#gb-form-save');
        modal.open();
        formSave.on('click', function (e) {
          var formData = serialize(formComment[0], {
            hash: true,
            empty: true
          });
          e.preventDefault();
          reqwest({
            url: ajaxurl,
            method: 'post',
            type: 'json',
            data: {
              action: 'add_comments',
              data: formData
            },
            success: function (resp) {
              modal.content(resp.data);

              if (resp.success) {
                window.location.reload();
              }
            }
          })
        });

      },
      error: function (err) {
        console.log(err);
      }
    });
  });
}