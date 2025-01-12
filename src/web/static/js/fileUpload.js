let fileInput = document.getElementById("file");
let selectedFile = document.getElementById("selected-file");
fileInput.addEventListener("change", () => {
    let file = fileInput.files[0];
    let fileName = file.name;
    let fileSize = (file.size / 1024).toFixed(1);
    selectedFile.innerHTML = `Selected: ${fileName} (${fileSize}KB)`;
});