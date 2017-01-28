<?php
$form = new Form([
  [
    'name'  => 'Sample',
    'title' => 'sample'
  ]
]);
?>

<div class="apeform">
  <?= $form->html() ?>
</div>

<?php
if ($form->isSubmitted()) {
  prr($form->getData());
}
?>

<script>
  new Ngn.Form(document.getElement('.apeform form'));
</script>