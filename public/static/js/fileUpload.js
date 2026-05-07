const fileInput = document.getElementById('file');
const selectedFile = document.getElementById('selected-file');

fileInput.addEventListener('change', () => {
  const file = fileInput.files[0];

  if (!file) {
    selectedFile.textContent = 'No file selected';
    return;
  }

  const fileSize = (file.size / 1024).toFixed(1);
  selectedFile.textContent = `Selected: ${file.name} (${fileSize} KB)`;
});
