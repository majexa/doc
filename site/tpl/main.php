<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta content="NGN" name="generator"/>
  <title><?= $d['pageHeadTitle'] ?></title>
  <link rel="stylesheet" href="/m/css/doc.css">
  <script>
    window.onload = function() {
      var toc = "";
      var level = 0;
      document.getElementById("contents").innerHTML = document.getElementById("contents").innerHTML.replace(/<h([\d])>(.+)<\/h([\d])>/gi, function(str, openLevel, titleText, closeLevel) {
        if (openLevel != closeLevel) {
          return str;
        }
        if (openLevel > level) {
          toc += (new Array(openLevel - level + 1)).join("<ul>");
        } else if (openLevel < level) {
          toc += (new Array(level - openLevel + 1)).join("</ul>");
        }
        level = parseInt(openLevel);
        var anchor = titleText.replace(/ /g, "_").replace(/<\/?[^>]+>/gi, '');
        var text, isLink = false;
        if (titleText.match(/<a href=/)) {
          isLink = true;
          text = titleText;
        } else {
          text = "<a href=\"#" + anchor + "\">" + titleText + "</a>";
        }
        toc += "<li>" + text + "</li>";
        if (isLink) return "<h" + openLevel + ">" + titleText + "</h" + closeLevel + ">";
        return "<h" + openLevel + "><a name=\"" + anchor + "\">" + titleText + "</a></h" + closeLevel + ">";
      });
      if (level) {
        toc += (new Array(level + 1)).join("</ul>");
      }
      if (window.location.pathname != '/') {
        toc = '<a href="/" class="toHome">На главную</a>' + toc;
      }
      document.getElementById("toc").innerHTML += toc;
    };
  </script>
  <link rel="stylesheet" href="/m/css/sublime.css">
  <script src="/m/js/highlight.min.js"></script>
  <script>
    hljs.initHighlightingOnLoad();
  </script>
</head>
<body>
<div id="toc"></div>
<div id="contents<?= $d['contentsClass']?>">
  <?= $d['html'] ?>
</div>
</body>
</html>