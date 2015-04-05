<?php

class PreMarkdown {

  static function process($file) {
    $text = file_get_contents($file);
    // apiPhp
    $text = preg_replace_callback('/( *){apiPhp (.*)}/', function ($m) {
      $api = new DocMethodsPhp($m[2]);
      $s = '';
      foreach ($api as $v) {
        $v['api'] = preg_replace('/^([a-zA-Z_]+)\(/', '[b]$1[/b](', $v['api']);
        $s .= $m[1].'- `'.$v['class'].'::'.$v['api'].';`<br>'.$v['title']."\n";
      }
      return '<div class="api" markdown="1"><div class="help">@api</div>'.$s.'</div>';
    }, $text);
    // apiJs
    $text = preg_replace_callback('/( *){apiJs (.*)}/', function ($m) {
      $api = new DocBlocksClassJs($m[2]);
      $s = '##'.$api['name'].'##'."\n\n";
      $s .= $api['descr']."\n\n";
      if ($api['arguments']) $s .= PreMarkdown::jsRenderArguments($api['arguments']);
      if ($api['options']) $s .= PreMarkdown::jsRenderOptions($api['options']);
      return $s;
    }, $text);
    // console
    $text = preg_replace_callback('/{console (.*)}/', function ($m) {
      if (preg_match('/\|(.*)\|(.*)/', $m[1], $m2)) {
        $text = $m2[1];
        $cmd = $m2[2];
      }
      else {
        $text = $m[1];
        $cmd = $m[1];
      }
      //$cmd = str_replace('$', '\\$', $cmd);
      $cmd = preg_replace('/^run /', 'php '.NGN_ENV_PATH.'/run/run.php ', $cmd);
      $cmdOutput = Ansi2Html::convert(`$cmd`);
      return <<<HTML
<div class="console">
  <div class="help">
    > <span class="cmd">$text</span>
  </div>
  $cmdOutput
</div>
HTML;
    }, $text);
    // class
    $text = preg_replace_callback('/{class (.*)}/', function ($m) {
      if (($path = Lib::getClassPath($m[1])) !== false) {
        $c = file_get_contents($path);
        $c = str_replace("<?php\n\n", '', $c);
        return self::pre($c);
      }
      else {
        return '<p style="color#f00">Class "'.$m[1].'" does not exists</p>';
      }
    }, $text);
    // clientSide
    $text = preg_replace_callback('/{clientSide (.*)}/', function ($m) {
      return '';
      return //
        self::pre(file_get_contents(PROJECT_PATH.'/tpl/js/'.$m[1].'.php')). //
        '<iframe src="/clientSide/'.$m[1].'" style="height:220px;border:0px;"></iframe>';
    }, $text);
    // file
    $text = preg_replace_callback('/{file (.*)}/', function ($m) {
      $c = file_get_contents($m[1]);
      $c = str_replace("<?php\n\n", '', $c);
      $c = self::pre($c);
      return $c;
    }, $text);
    // daily-ngn-cst
    $text = preg_replace_callback('/{daily-ngn-cst (.*)}/', function ($m) {
      $path = '/m/daily-ngn-cst/'.$m[1];
      $folder = WEBROOT_PATH.'/m/daily-ngn-cst/'.$m[1];
      if (!file_exists($folder)) return "folder '$folder' does not exists";
      $s = '';
      $r = glob($folder.'/*');
      foreach ($r as $file) {
        $s .= '<img src="'.$path.'/'.basename($file).'" style="width:100px">';
      }
      return $s;
    }, $text);
    // tag
    $text = preg_replace_callback('/{tag (.*)}/', function ($m) {
      return '';
    }, $text);
    // tpl
    $text = preg_replace_callback('/{tpl (.*)}/', function ($m) {
      ob_start();
      require PROJECT_PATH.'/tpl/'.$m[1].'.php';
      $c = ob_get_contents();
      ob_end_clean();
      return $c;
    }, $text);
    // phpCode
    $text = preg_replace_callback('/{{phpCode (.*)}}/sm', function ($m) {
      $c = self::pre($m[1]);
      ob_start();
      eval($m[1]);
      $r = ob_get_clean();
      if ($r) $c .= "\n$r";
      return self::pre($c);
    }, $text);

    return $text;
  }

  static function pre($c) {
    $arr = explode("\n", $c);
    foreach ($arr as $key => $value) {
      $arr[$key] = '    '.$arr[$key];
    }
    return "\n".implode("\n", $arr)."\n";
  }

  static function jsRenderArguments(array $r) {
    return self::jsRenderParams($r, 'Аргументы');
  }

  static function jsRenderOptions(array $r) {
    return self::jsRenderParams($r, 'Опции');
  }

  protected static function jsRenderParams(array $r, $title) {
    $s = "####$title####\n\n";
    foreach ($r as $v) {
      $v['type'] = self::jsRenderType($v['type']);
      $s .= ' - '.$v['name'].($v['type'] ? ' <span class="gray">('.$v['type'].')</span>' : '').($v['descr'] ? ' — '.$v['descr'] : '')."\n";
    }
    $s .= "\n";
    return $s;
  }

  static $mootoolsClasses = [
    'Class',
    'Element',
    'Array',
    'Function',
    'Number',
    'String',
  ];

  static $mootoolsClassParents = [
    'Array' => 'Types',
    'Function' => 'Types',
    'String' => 'Types',
  ];

  static protected function jsRenderType($type) {
    $r = [];
    foreach (explode('|', $type) as $t) {
      if (strstr($t, '.')) $tt = explode('.', $t);
      else $tt = [isset(self::$mootoolsClassParents[$t]) ? self::$mootoolsClassParents[$t] : $t, $t];
      if (in_array($t, self::$mootoolsClasses)) {
        $t = '<a href="http://mootools.net/core/docs/1.5.1/'.implode('/', $tt).'" target="_blank">'.$t.'</a>';
      }
      $r[] = $t;
    }
    return implode('|', $r);
  }

}