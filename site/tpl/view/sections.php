<style>
  .toc {

  }
  .toc h4 {
    margin-bottom: 5px;
  }
  .toc > ul > li {
    margin-right: 20px;
  }
  .toc > ul > li h3 {
    color: #0055C4;
    border-bottom: 1px solid #EBE5C8;
  }
</style>
<div class="toc">
  <h2>Поддерживаются следующие типы полей формы:</h2>
  <ul>
  <? foreach (ClassCore::getNames('FieldE') as $name) { ?>
    <li><?= $name ?></li>
  <? } ?>
  </ul>
  <? foreach ($d['docs'] as $section) { ?>
    <h2><?= $section['title'] ?></h2>
    <ul>
      <? foreach ($section['items'] as $v) { ?>
        <li>
          <?= $v['text'] ?>
          <? if ($v['cmd']) { ?>
            <div class="gray"><?= $v['cmd'] ?></div>
          <? } ?>
        </li>
      <? } ?>
    </ul>
  <? } ?>
</div>