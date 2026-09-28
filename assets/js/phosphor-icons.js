// Self-hosted Phosphor icon styles. Only the weights the site actually uses
// (regular "ph", "ph-bold", "ph-fill") are loaded - the thin/light/duotone
// sheets were ~330KB of unused CSS pulled from unpkg on every page.
var head = document.getElementsByTagName("head")[0];
var phosphorBase =
  (document.querySelector('meta[name="base-url"]') || {}).content || "/";

for (var weight of ["regular", "bold", "fill"]) {
  var link = document.createElement("link");
  link.rel = "stylesheet";
  link.type = "text/css";
  link.href = phosphorBase + "assets/fonts/phosphor/" + weight + "/style.css";
  head.appendChild(link);
}
