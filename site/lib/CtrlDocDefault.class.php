<?php

class CtrlDocDefault extends CtrlDefault {

  protected function getParamActionN() {
    return 0;
  }

  protected $defaultAction = 'doc';

  function action_doc() {
    if (empty($this->req->params[1])) {
      $_p = 'index';
      $p = DATA_PATH.'/docTpl/'.$_p;
    }
    else {
      $_p = implode('/', array_slice($this->req->params, 1, count($this->req->params)));
      $p = DATA_PATH.'/docTpl/'.$_p;
    }
    $nmdFile = $p.'.md';
    if (file_exists($nmdFile)) {
      $this->d['html'] = (new NgnMarkdown)->html(file_get_contents($nmdFile));
      if (file_exists(DATA_PATH.'/sourceDocs/'.$_p.'.md')) {
        $this->d['html'] .= (new NgnMarkdown)->html(file_get_contents( //
          DATA_PATH.'/sourceDocs/'.$_p.'.md' //
        ));
      }
    }
    elseif (file_exists($p.'.php')) {
      $this->d['html'] = Misc::getIncluded($p.'.php');
    }
    else {
      throw new Exception("path $p not found");
    }
  }

  function action_cpanel() {
    $css = <<<CSS
<style>
.menu ul {
  text-align: left;
  display: inline;
  margin: 0;
  padding: 15px 4px 17px 0;
  list-style: none;
  -webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
  -moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
}
.menu ul li {
  font: bold 12px/18px sans-serif;
  display: inline-block;
  margin-right: -4px;
  position: relative;
  padding: 15px 20px;
  background: #fff;
  cursor: pointer;
  -webkit-transition: all 0.2s;
  -moz-transition: all 0.2s;
  -ms-transition: all 0.2s;
  -o-transition: all 0.2s;
  transition: all 0.2s;
}
.menu ul li:hover {
  background: #555;
  color: #fff;
}
.menu ul li ul {
  padding: 0;
  position: absolute;
  top: 48px;
  left: 0;
  width: 250px;
  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;
  display: none;
  opacity: 0;
  visibility: hidden;
  -webkit-transiton: opacity 0.2s;
  -moz-transition: opacity 0.2s;
  -ms-transition: opacity 0.2s;
  -o-transition: opacity 0.2s;
  -transition: opacity 0.2s;
}
.menu ul li ul li {
  padding: 5px 20px;
  background: #555;
  display: block;
  color: #fff;
  text-shadow: 0 -1px 0 #000;
}
.menu ul li ul li:hover { background: #666; }
.menu ul li:hover ul {
  display: block;
  opacity: 1;
  visibility: visible;
}

#cmdResult {
height: 300px;
margin-top: 10px;
overflow-y: scroll;
}
</style>
CSS;
    $js = <<<JS
<script>
window.addEvent('domready', function() {
  document.getElement('.menu').getElements('ul li').each(function(eLi) {
    var cmdName = eLi.get('data-cmdName');
    eLi.getElements('li').each(function(eLi2) {
      eLi2.addEvent('click', function() {
        var runCmd = function() {
          $('cmdResult').addClass('hLoader');
          new Ngn.Request({
            url: '/ajax_runCmd?cmd=' + cmdName + '%20' + eLi2.get('data-method'),
            onComplete: function(cmdResult) {
              $('cmdResult').removeClass('hLoader').set('html', cmdResult);
            }
          }).send();
        };
        new Ngn.Request({
          url: '/ajax_cmdHasParams/' + cmdName + '/' + eLi2.get('data-method'),
          onComplete: function(cmdHasParams) {
            if (cmdHasParams) {
              new Ngn.Dialog.RequestForm({
                url: '/json_cmdDialog/' + cmdName + '/' + eLi2.get('data-method'),
                width: 300,
                onFormRequest: function() {
                  //$('cmdResult').addClass('hLoader');
                },
                onFormResponse: function(r) {
                  //$('cmdResult').removeClass('hLoader');
                },
                onSubmitSuccess: function(r) {
                  $('cmdResult').set('html', r.cmdResult || 'NO RESULT');
                }
              });
            } else {
              runCmd();
            }
          }
        }).send();
      });
    });
  });
});
</script>
JS;

    $this->d['html'] = $css.$js.'<div class="menu">'.(new PmManagerWrapper)->html().'</div><div id="cmdResult"></div>';
  }

  function action_ajax_cmdHasParams() {
    $this->ajaxOutput = (new PmManagerWrapper)->cmdHasParams($this->req->param(1), $this->req->param(2));
  }

  function action_json_cmdDialog() {
    $form = (new PmManagerWrapper)->getForm($this->req->param(1), $this->req->param(2));
    $form->action = $this->req->path;
    if ($form->isSubmittedAndValid()) {
      sleep(1);
      $cmdName = $this->req->param(1);
      $method = $this->req->param(2);
      $cmd = $cmdName.' '.$method.' '.implode(' ', $form->getData());
      $this->json['cmdResult'] = Ansi2Html::convert(`pm $cmd`);
      return false;
    }
    return $form;
  }

  function action_ajax_runCmd() {
    $this->ajaxOutput = Ansi2Html::convert(`pm {$this->req['cmd']}`);
  }

  function action_tags() {
    $r = [];
    foreach (glob(DATA_PATH.'/docTpl/*') as $file) {
      $c = file_get_contents($file);
      if (preg_match_all('/{tag (.*)}/', $c, $m)) {
        foreach ($m[1] as $v) {
          $r[] = [
            'title' => $v,
            'name'  => Misc::removePrefix('tpl.', Misc::removeSuffix('.md', basename($file))),
          ];
        }
      }
    }
    foreach ($r as &$v) $v['link'] = '/doc/'.$v['name'];
    $r = Arr::sortByOrderKey($r, 'title');
    $this->d['contentsClass'] = ' tags';
    $this->d['html'] = Tt()->getTpl('cp/links', $r);
  }

  function action_componentDemo() {
    $this->d['mainTpl'] = 'jsMain';
    $tplPath = ltrim($this->req->path(1), '/');
    list($jsClass) = explode('/', $tplPath);
    $dep = new JsCssDependencies($jsClass);
    $c = '';
    foreach ($dep->r as $package => $paths) {
      foreach ($paths as $path) {
        $c .= $dep->css->getFileContents($dep->css->getAbsPath($path));
      }
    }
    Dir::make(UPLOAD_PATH.'/component/css');
    file_put_contents(UPLOAD_PATH.'/component/css/'.$jsClass.'.css', $c);
    $cssPath = '/'.UPLOAD_DIR.'/component/css/'.$jsClass.'.css';
    // ==============
    $this->d['html'] = '<link rel="stylesheet" href="'.$cssPath.'" />'."\n\n";

    $jsDoc = new DocBlockMtClassJs($jsClass);
    if (isset($jsDoc['descrParams']['example'])) {
      $this->d['html'] .= '<script>'.$jsDoc['descrParams']['example'].'</script>';
    } else {
      $this->d['html'] .= Misc::getIncluded(DOC_PATH.'/tpl/js/'.$tplPath.'.php');
    }
  }

  function action_component() {
    $csbuildHost = 'csbuild.311.su';
    $component = $this->req->param(1);
    $this->setPageTitle("Скачать компонент $component");
    $r = json_decode(file_get_contents("http://$csbuildHost/json_dependencies/$component"), JSON_FORCE_OBJECT);

    $r['size'] = File::format2($r['size']);
    $r['compressedSize'] = File::format2($r['compressedSize']);

    // css
    $css = new SflmCss;

    $ngnJsDependencies = array_map(function ($v) use ($r) {
      return ltrim($v, '- ');
    }, explode("\n", trim($r['dependencies']['ngn'])));
    $paths = [];
    $paths['common'] = $css->getPaths('common');
    foreach ($ngnJsDependencies as $_component) {
      $lib = JsCssDependencies::cssLib($_component);
      if ($_paths = $css->getPaths($lib)) {
        $paths[$lib] = $_paths;
      }
    }

    $cssPaths = "<h2>CSS</h2>\n";
    foreach ($paths as $lib => $paths2) {
      $cssPaths .= '<b><label for="'.$lib.'"><input type="checkbox" id="'.$lib.'" name="component" checked disabled>&nbsp;'.$lib.'</label></b>';
      $cssPaths .= '<ul>';
      foreach ($paths2 as $path) {
        $cssPaths .= "<li>$path</li>";
      }
      $cssPaths .= '</ul>';
    }

    $cssPaths .= '<a href="http://'.$csbuildHost.'/ajax_downloadCss/'.$component.'" class="btn"><span>Скачать&nbsp;собранный</span></a>';

    $this->d['html'] = <<<HTML
<link rel="stylesheet" type="text/css" href="/i/css/common/btns.css" />
<style>
pre {
font-size:10px;
}
</style>
<h1>Скачать компонент $component</h1>
<div style="float:left;width:240px;">
  <h2>MooTools зависимости</h2><pre>{$r['dependencies']['mt']}</pre>
</div>
<div style="float:left;width:240px;">
  <h2>Ngn зависимости</h2><pre>{$r['dependencies']['ngn']}</pre>
</div>
<div style="float:left;width:100px;">
  <h2>Скачать</h2>
  <p>Оригинал {$r['size']}</p>
  <p><a href="http://$csbuildHost/ajax_download/$component" class="btn"><span>Скачать</span></a></p>
  <hr>
  <p>Сжатый {$r['compressedSize']}</p>
  <p><a href="http://$csbuildHost/ajax_downloadCompressed/$component" class="btn"><span>Скачать</span></a></p>
  <h2><a href="/doc/clientSide#$component">API</a></h2>
</div>
<div style="clear:both">
  $cssPaths
</div>

<div style="clear:both;">&nbsp;</div>
HTML;
  }

}