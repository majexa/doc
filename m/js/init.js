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
