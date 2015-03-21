<?php

class CtrlDocDefault extends CtrlCommon {

  protected function getParamActionN() {
    return 0;
  }

  protected $defaultAction = 'doc';

  function action_doc() {
    if (empty($this->req->params[1])) {
      $p = DATA_PATH.'/docTpl/index';
    } else {
      $p = implode('/', array_slice($this->req->params, 1, count($this->req->params)));
      $p = DATA_PATH.'/docTpl/'.$p;
    }
    if (file_exists($p.'.md')) {
      $this->d['html'] = DocCore::markdown($p.'.md');
    }
    elseif (file_exists($p.'.php')) {
      $this->d['html'] = Misc::getIncluded($p.'.php');
    } else {
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

  function action_clientSide() {
    $this->d['mainTpl'] = 'jsMain';
    $this->d['tpl'] = 'js/'.$this->req->param(1);
  }

}