<?php

require __DIR__.'/Michelf/Markdown.inc.php';
require __DIR__.'/Michelf/MarkdownExtra.inc.php';

use \Michelf\MarkdownExtra;

/**
 * Класс `NgnMarkdown` отвечает за рендеринг HTML-кода из формата _NgnMarkdown_,
 * который в свою очередь базируется на расширенном формате Markdown'а — _MarkdownExtra_.
 * Он предоставляющий некоторые дополнительные возможности, для тюнинга HTML-рендероинга.
 * Смотрите подробнее на [сайте библиотеки](https://michelf.ca/projects/php-markdown/extra/)
 *
 * Формат _NgnMarkdown_ ...
 *
 * __Markdown__
 * <pre>
 * - asdsad&lt;br&gt;sad
 * - asd
 * </pre>
 * __MarkdownExtra__
 *
 * <pre>
 * ~~~~
 * asdsad&lt;br&gt;sad
 * ~~~~
 * </pre>
 *
 * __NgnMarkdown__
 * <pre>
 * ~~~~
 * asdsad&lt;br&gt;sad
 * ~~~~
 * {apiPhp ClassName}
 * </pre>
 *
 */
class NgnMarkdown {

  /**
   * Преобразовывает формат NgnMarkdown в MarkdownExtra
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown
   */
  function markdownExtra($NgnMarkdown) {
    $NgnMarkdown = $this->markdownFile($NgnMarkdown);
    $NgnMarkdown = $this->markdownTpl($NgnMarkdown);
    $NgnMarkdown = $this->markdownPhpCode($NgnMarkdown);
    $NgnMarkdown = $this->markdownClass($NgnMarkdown);
    $NgnMarkdown = $this->markdownApiPhp($NgnMarkdown);
    $NgnMarkdown = $this->markdownApiJs($NgnMarkdown);
    $NgnMarkdown = $this->markdownConsole($NgnMarkdown);
    //$NgnMarkdown = $this->markdownClientSide($NgnMarkdown);
    $NgnMarkdown = $this->markdownMarkers($NgnMarkdown);
    $markdownExtra = $this->markdownDailyNgnCst($NgnMarkdown);
    // now its MarkdownExtra (not NgnMarkdown)
    return $markdownExtra;
  }

  /**
   * Преобразовывает формат NgnMarkdown в Markdown
   *
   * @param string $meText MarkdownExtra
   * @return string
   */
  function markdown($meText) {
    return $this->markdownExtra($meText);
  }

  /**
   * Преобразовывает формат NgnMarkdown в HTML
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return mixed HTML
   */
  function html($NgnMarkdown) {
    $markdown = $this->markdownExtra($NgnMarkdown);
    $html = MarkdownExtra::defaultTransform($markdown);
    $html = str_replace('[b]', '<b>', $html);
    $html = str_replace('[/b]', '</b>', $html);
    $html = preg_replace('/<code>\s*SQL:\s*/s', '<code class="sql">', $html);
    $html = str_replace('    <code>', '<code class="php">', $html);
    $html = str_replace('<table>', '<table cellspacing=0>', $html);
    return $html;
  }

  /**
   * @api
   * Возвращает текст в формате Markdown, преобразованный из текстовых блоков
   * вида {apiPhp ClassName} в API из DocBlock комментариев
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string MarkdownExtra
   */
  protected function markdownApiPhp($NgnMarkdown) {
    return preg_replace_callback('/{apiPhp (.*)}/', function ($m) {
      $class = $m[1];
      $api = new DocMethodsPhp($class, true, false);
      $s = '';
      foreach ($api as $v) {
        $v['api'] = preg_replace('/^([a-zA-Z_]+)\(/', '__$1__(', $v['api']);
        $v['title'] = str_replace('{this}', '(new '.$v['class'].')->', $v['title']);
        $s .= '- (new '.$v['class'].')->'.$v['api']."<br><i style='color:#666'>".$v['title']."</i>\n";
        if (!empty($v['params'])) {
          foreach ($v['params'] as $param) {
            $s .= "    - {$param['type']} __{$param['name']}__".($param['descr'] ? " — _{$param['descr']}_" : '')."\n";
          }
        }
      }
      return (new DocClassPhp($class)). //
      '<div class="api" markdown="1"><div class="help">@api</div>'.$s.'</div>';
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {apiJs Ngn.ClassName} в API
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown
   */
  protected function markdownApiJs($NgnMarkdown) {
    return preg_replace_callback('/( *){apiJs (.*)}/', function ($m) {
      $api = new DocBlocksClassJs($m[2]);
      $s = '##'.$api['name'].'##'."\n\n";
      $s .= $api['descr']."\n\n";
      if ($api['arguments']) $s .= $this->renderJsArguments($api['arguments']);
      if ($api['options']) $s .= $this->renderJsOptions($api['options']);
      return $s;
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {console ngnCommand} в результат вывода этих команд, преобразованый в HTML
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown + HTML
   */
  protected function markdownConsole($NgnMarkdown) {
    return preg_replace_callback('/{console (.*)}/', function ($m) {
      if (preg_match('/\|(.*)\|(.*)/', $m[1], $m2)) {
        $text = $m2[1];
        $cmd = $m2[2];
      }
      else {
        $text = $m[1];
        $cmd = $m[1];
      }
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
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {class phpClassName} в листинг этого класса
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown
   */
  protected function markdownClass($NgnMarkdown) {
    return preg_replace_callback('/{class (.*)}/', function ($m) {
      if (($path = Lib::getClassPath($m[1])) !== false) {
        $c = file_get_contents($path);
        $c = str_replace("<?php\n\n", '', $c);
        return $this->pre($c);
      }
      else {
        return '<p style="color#f00">Class "'.$m[1].'" does not exists</p>';
      }
    }, $NgnMarkdown);
  }

  protected function markdownClientSide($NgnMarkdown) {
    return preg_replace_callback('/{clientSide (.*)}/', function ($m) {
      return //
        $this->pre(file_get_contents(PROJECT_PATH.'/tpl/js/'.$m[1].'.php')). //
        '<iframe src="/clientSide/'.$m[1].'" style="height:220px;border:0px;"></iframe>';
    }, $NgnMarkdown);
  }

  protected function markdownMarkers($ngnMarkdown) {
    $ngnMarkdown = preg_replace('/^\^(.*)$/m', '<p class="important" markdown="1">$1</p>', $ngnMarkdown);
    $ngnMarkdown = preg_replace('/^\^\^(.*)$/m', '<p class="panel" markdown="1">$1</p>', $ngnMarkdown);
    return $ngnMarkdown;
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {file phpFilePath} в листинг этого файла
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown
   */
  protected function markdownFile($NgnMarkdown) {
    return preg_replace_callback('/{file (.*)}/', function ($m) {
      $c = file_get_contents($m[1]);
      $c = str_replace("<?php\n\n", '', $c);
      $c = $this->pre($c);
      return $c;
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {daily-ngn-cst name} в картинку этого ежедневного client-side теста
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown + HTML
   */
  protected function markdownDailyNgnCst($NgnMarkdown) {
    return preg_replace_callback('/{daily-ngn-cst (.*)}/', function ($m) {
      $path = '/m/daily-ngn-cst/'.$m[1];
      $folder = WEBROOT_PATH.'/m/daily-ngn-cst/'.$m[1];
      if (!file_exists($folder)) return "folder '$folder' does not exists";
      $s = '';
      $r = glob($folder.'/*');
      foreach ($r as $file) {
        $s .= '<img src="'.$path.'/'.basename($file).'" style="width:100px">';
      }
      return $s;
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {tpl path} в интерпретированый файл шаблона проекта
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown + HTML
   */
  protected function markdownTpl($NgnMarkdown) {
    return preg_replace_callback('/{tpl (.*)}/', function ($m) {
      ob_start();
      require PROJECT_PATH.'/tpl/'.$m[1].'.php';
      $c = ob_get_contents();
      ob_end_clean();
      return $c;
    }, $NgnMarkdown);
  }

  /**
   * Возвращает текст в формате markdown преобразованный из текстовых блоков
   * формата {phpCode code} в результат eval'а этого кода
   *
   * @param string $NgnMarkdown Текст в формате NgnMarkdown
   * @return string Markdown + HTML
   */
  protected function markdownPhpCode($NgnMarkdown) {
    return preg_replace_callback('/{{phpCode (.*)}}/sm', function ($m) {
      $c = $this->pre($m[1]);
      ob_start();
      eval($m[1]);
      $r = ob_get_clean();
      if ($r) $c .= "\n$r";
      return $this->pre($c);
    }, $NgnMarkdown);
  }

  protected function markdownTag($NgnMarkdown) {
    return $NgnMarkdown;
  }

  protected function pre($c) {
    $arr = explode("\n", $c);
    foreach ($arr as $key => $value) {
      $arr[$key] = '    '.$arr[$key];
    }
    return "\n".implode("\n", $arr)."\n";
  }

  protected function renderJsArguments(array $r) {
    return $this->renderJsParams($r, 'Аргументы');
  }

  protected function renderJsOptions(array $r) {
    return $this->renderJsParams($r, 'Опции');
  }

  protected function renderJsParams(array $r, $title) {
    $s = "####$title####\n\n";
    foreach ($r as $v) {
      $v['type'] = $this->renderJsType($v['type']);
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
    'Array'    => 'Types',
    'Function' => 'Types',
    'String'   => 'Types',
  ];

  protected function renderJsType($type) {
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