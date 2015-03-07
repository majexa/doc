<?php

class PreMarkdown {

  static function process($file) {
    $text = file_get_contents($file);
    // api
    $text = preg_replace_callback('/( *){api (.*)}/', function ($m) {
      $api = new DocBlocksClass($m[2]);
      $s = "";
      foreach ($api as $v) {
        $v['api'] = preg_replace('/^([a-zA-Z_]+)\(/', '[b]$1[/b](', $v['api']);
        $s .= $m[1].'- `'.$v['class'].'::'.$v['api'].';`<br>'.$v['title']."\n";
      }
      return '<div class="api" markdown="1"><div class="help">@api</div>'.$s.'</div>';
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
      $cmd = str_replace('$', '\\$', $cmd);
      return '<div class="console"><div class="help">> <span class="cmd">'.$text.'</span></div>'.Ansi2Html::convert(`$cmd`).'</div>';
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
    return $text;
  }

  static function pre($c) {
    $arr = explode("\n", $c);
    foreach ($arr as $key => $value) {
      $arr[$key] = '    '.$arr[$key];
    }
    return "\n".implode("\n", $arr)."\n";
  }

}