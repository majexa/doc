<?

function smart_wordwrap($string, $width = 75, $break = "\n") {
  // split on problem words over the line length
  $pattern = sprintf('/([^ ]{%d,})/', $width);
  $output = '';
  $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

  foreach ($words as $word) {
    if (false !== strpos($word, ' ')) {
      // normal behaviour, rebuild the string
      $output .= $word;
    }
    else {
      // work out how many characters would be on the current line
      $wrapped = explode($break, wordwrap($output, $width, $break));
      $count = $width - (strlen(end($wrapped)) % $width);

      // fill the current line and add a break
      $output .= substr($word, 0, $count).$break;

      // wrap any remaining characters from the problem word
      $output .= wordwrap(substr($word, $count), $width, $break, true);
    }
  }

  // wrap the final output
  return wordwrap($output, $width, $break);
}

function f($code) {
  $code = str_replace('##', "'", $code);
  print '<pre><code class="php">';
  print $code;
  print '</code></pre>';
  print '<pre><code class="php">';
  $code = str_replace('print_r(', 'print_rr(', $code);
  eval($code);
  print '</code></pre>';
}

function print_rr(array $d) {
  $r = smart_wordwrap(var_export($d, true), 80);
  print htmlspecialchars($r);
}

?>
<style>
  #toc {
    display: none;
  }
  #contents {
    max-width: 1000px;
  }
  .fullClass {
    width: 530px;
    float: right;
  }
  .fullClass .hljs {
    font-size: 8px;
  }
  .fullClass pre {
  }
  .cookbook pre {
    width: 450px;
  }
</style>

<!--
<div class="fullClass">
  <pre>
    <code class="php">
<?= str_replace("<?php\n\n", '', htmlspecialchars(file_get_contents(Lib::getClassPath('Pagination')))) ?>
    </code>
  </pre>
</div>
-->

<h2>St или Simple Template Engines</h2>
<p>St - это набор из трёх инлайновых шаблонных процессоров. Инлайн в этом случае означает, что текст
  шаблона используется прямо в коде в виде строковых значений.</p>
<h3>Шаблонный процессор TTTT</h3>
<?

/*
 * - сигнатура
 * - описание
 * - аргументы, опции
 * - возвращает
 * - примеры
 * - заметки
 */


die2(new DocBlocksClassPhp('St', false));
?>
<pre>
  <code class="php">
    
  </code>
</pre>

<h2>Стандартное поведение</h2>
<div class="cookbook">
  Класс пагинации.<br>
  Задачи: Вывод ссылок на страницы. Автоматическая генерация HTML ссылок<br>
  <?
  f('
print_r((new Pagination([
  ##db## => new DemoDb
]))->get(##dd_i_portfolio##));
');
  ?>
</div>