<?php

class PmManagerWrapper extends PmManager {

  function __construct() {
    parent::__construct('');
  }

  protected function init() {
  }

  protected function defineOptions() {
    return [
      'disableRun' => true
    ];
  }

  function html() {
    $tree = [];
    foreach ($this->getClasses() as $v) {
      $tree[] = $this->getNode($v['class']);
    }
    return Tt()->getTpl('common/ul', $tree);
  }

  protected function formatTitle($t) {
    return ucfirst(trim(str_replace('-', ' ', Misc::hyphenate($t))));
  }

  protected function getNode($class) {
    $cmdName = $this->cmdName($class);
    $node = [
      'title'    => $this->formatTitle($cmdName),
      'data' => [
        'cmdName' => $cmdName
      ],
      'children' => []
    ];
    foreach ($this->getMethods($class) as $method) {
      $node['children'][] = [
        'title' => $this->formatTitle($method['method']),
        'data' => [
          'method' => $method['method']
        ]
      ];
    }
    return $node;
  }

  function cmdHasParams($cmdName, $method) {
    $class = Arr::getSubValue($this->getClasses(), 'name', $cmdName, 'class');
    $r = Arr::getValueByKey($this->getMethods($class), 'method', $method);
    return !empty($r['options']);
  }

  function getForm($cmdName, $method) {
    $class = Arr::getSubValue($this->getClasses(), 'name', $cmdName, 'class');
    $r = Arr::getValueByKey($this->getMethods($class), 'method', $method);
    $fields = [];

    foreach ($r['options'] as $opt) {
      $method = 'paramOptions_'.$opt['name'];
      $method2 = 'helpOpt_'.$opt['name'];
      $field = [
        'title' => $this->formatTitle($opt['name']),
        'name' => $opt['name'],
        'required' => true
      ];
      if (method_exists($class, $method)) {
        $field['type'] = 'select';
        $field['options'] = $class::$method();
      } elseif (method_exists($class, $method2)) {
        $field['type'] = 'select';
        $field['options'] = $class::$method2();
      }
      $fields[] = $field;
    }
    return new Form($fields, [
      'submitTitle' => 'OK',
      'title' => $r['title']
    ]);
  }

}