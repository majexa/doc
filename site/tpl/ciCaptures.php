<? foreach (glob(NGN_ENV_PATH.'/ci/web/captures/*.png') as $v) { ?>
  <img src="//ci.majexa.ru/captures/<?= basename($v) ?>" />
<? } ?>