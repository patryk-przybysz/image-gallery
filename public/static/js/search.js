let controller = null;

let gallery = document.getElementById("gallery");
let previousQ = "";

document.getElementById("q").addEventListener("keyup", (e) => {
  let q = e.target.value;

  if (q == previousQ) return;
  previousQ = q;

  if (controller) {
    controller.abort();
  }

  controller = new AbortController();

  fetch("/api/search", {
    method: "POST",
    body: new URLSearchParams({ q }),
    signal: controller.signal,
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((res) => res.text())
    .then((html) => {
      gallery.innerHTML = html;
    })
    // Silence the abort error
    .catch(($e) => {});
});
