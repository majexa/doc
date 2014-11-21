<style>
  .toc {

  }
  .toc h4 {
    margin-bottom: 5px;
  }
  .toc > ul > li {
    list-style: none;
    margin-right: 20px;
  }
  .toc > ul > li h3 {
    color: #0055C4;
    border-bottom: 1px solid #EBE5C8;
  }
</style>
<div class="toc">
  <? foreach ($d['docs'] as $package => $files) { ?>
    <h2><?= $package ?></h2>
    <ul>
      <? foreach ($files as $f) { ?>
        <li>
          <h3><?= $f['class'] ?></h3>
          <ul>
            <? foreach ($f['methods'] as $m) { ?>
              <li>
                <h4><?= $m['method'] ?></h4>
                <div class="dgray"><?= $m['comment']['text'] ?></div>
              </li>
            <? } ?>
          </ul>
        </li>
      <? } ?>
    </ul>
  <? } ?>
</div>