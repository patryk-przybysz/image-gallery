let controller = null;

const gallery = document.getElementById('gallery');
const searchInput = document.getElementById('q');
const status = document.getElementById('search-status');
let previousQ = '';

function setStatus(message) {
  if (status) status.textContent = message;
}

searchInput.addEventListener('input', (e) => {
  const q = e.target.value;

  if (q === previousQ) return;
  previousQ = q;

  if (controller) {
    controller.abort();
  }

  if (!q.trim()) {
    gallery.innerHTML = '';
    setStatus('Type to search the gallery.');
    return;
  }

  setStatus('Searching…');
  gallery.setAttribute('aria-busy', 'true');
  controller = new AbortController();

  fetch('/api/search', {
    method: 'POST',
    body: new URLSearchParams({ q }),
    signal: controller.signal,
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  })
    .then((res) => res.text())
    .then((html) => {
      gallery.innerHTML = html;
      gallery.removeAttribute('aria-busy');
      setStatus(gallery.textContent.trim() ? `Showing results for “${q}”.` : 'No matches found.');
    })
    .catch((error) => {
      gallery.removeAttribute('aria-busy');
      if (error.name !== 'AbortError') {
        setStatus('Search failed. Please try again.');
      }
    });
});
