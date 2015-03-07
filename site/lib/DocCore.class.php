<?php

require __DIR__.'/Michelf/Markdown.inc.php';
require __DIR__.'/Michelf/MarkdownExtra.inc.php';

use \Michelf\MarkdownExtra;

class DocCore {

  static function filesR($folder) {
    $r = [];
    foreach (glob("$folder/*") as $f) {
      if (is_dir($f)) {
        $r = array_merge($r, self::filesR($f));
        continue;
      }
      $r[] = $f;
    }
    return $r;
  }

  static $limit = 0;

  static function ngnEnvDocs() {
    $r = [];
    $n = 0;
    foreach (glob(NGN_ENV_PATH.'/*', GLOB_ONLYDIR) as $folder) {
      $package = basename($folder);
      foreach (self::filesR($folder) as $file) {
        if (!Misc::hasSuffix('.class.php', $file)) continue;
        if (!($doc = new DocBlocksFile($file))) continue;
        if (!($methods = $doc->methods())) continue;
        if (!isset($r[$package])) $r[$package] = [];
        $r[$package][] = [
          'methods'   => $methods,
          'class' => Misc::removeSuffix('.class.php', basename($file)),
          'file'  => $file
        ];
        $n++;
        if (self::$limit and $n >= self::$limit) break;
      }
    }
    return $r;
  }

  static function groupBySection($docs) {
    $r = [];
    foreach ($docs as $files) {
      foreach ($files as $file) {
        foreach ($file['methods'] as $method) {
          if (!isset($method['comment']['sections'])) continue;
          $section = $method['comment']['sections'][0];
          if (!isset($r[$section])) {
            $r[$section] = [
              'title' => self::getSectionTitle($section),
              'items' => []
            ];
          }
          if (!empty($method['comment']['cmd'])) {
            if (!is_array($method['comment']['cmd'])) $runner = NGN_ENV_PATH.'/run/run.php';
            $method['comment']['cmd'] = "php $runner \"(new {$file['class']})->{$method['method']}()\"";
          } else {
            $method['comment']['cmd'] = "{$file['class']}::{$method['method']}()";
          }
          unset($method['comment']['sections']);
          $r[$section]['items'][] = $method['comment'];
        }
      }
    }
    return $r;
  }

  static protected function getSectionTitle($k) {
    $toc = sfYaml::load(file_get_contents(PROJECT_PATH.'/toc.yaml'));
    return $toc[$k];
  }

  static function markdown($file) {
    $html = MarkdownExtra::defaultTransform(PreMarkdown::process($file));
    $html = str_replace('[b]', '<b>', $html);
    $html = str_replace('[/b]', '</b>', $html);
    $html = str_replace('<p>^^', '<p class="panel">', $html);
    $html = str_replace('<p>^', '<p class="important">', $html);
    $html = preg_replace('/<code>\s*SQL:\s*/s', '<code class="sql">', $html);
    $html = str_replace('<code>', '<code class="php">', $html);
    $html = str_replace('<table>', '<table cellspacing=0>', $html);
    return $html;
  }

}