const fileInput = document.getElementById('File-Input');
const fileNameDisplay = document.getElementById('File-Name');

if (fileInput && fileNameDisplay) {
    const defaultText = fileNameDisplay.textContent.trim();

    fileInput.addEventListener('change', () => {
        const selectedFile = fileInput.files?.[0];
        fileNameDisplay.textContent = selectedFile ? selectedFile.name : defaultText;
    });
}
