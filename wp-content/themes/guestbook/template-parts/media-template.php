<script type="text/html" id="tmpl-modal-comment-delet">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Видалити коментар</h4>
  </div>
  <div class="modal-body">
    <p>{{ data.content }}</p>
  </div>
  <div class="modal-footer">
    <div class="btn-group">
      <a href="{{ data.href }}" class="btn btn-danger delete"><?= __('Видалити') ?></a>
      <a href="#" class="btn btn-primary" data-dismiss="modal"><?= __('Скасувати') ?></a>
    </div>
  </div>
</script>