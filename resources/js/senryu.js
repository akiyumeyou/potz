document.addEventListener('DOMContentLoaded', function () {
    let dropArea = document.getElementById('drop-area');
    let fileElem = document.getElementById('fileElem');
    let fileNameDisplay = document.getElementById('file-name');
    let previewContainer = document.getElementById('preview-container');
    let reselectBtn = document.getElementById('reselect-btn');
    let toukouBtn = document.getElementById('toukou-btn');
    let currentFileSelected = false; // 画像が選択されているかを示すフラグ

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);
    fileElem.addEventListener('change', handleFiles, false);

    reselectBtn.addEventListener('click', function () {
        resetPreview();
        fileElem.value = '';
        currentFileSelected = false;
        dropArea.style.display = 'block';
        reselectBtn.style.display = 'none';
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;

        handleFiles({ target: { files: files } });
    }

    function handleFiles(e) {
        let files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            fileNameDisplay.innerText = file.name;
            previewFile(file);
            currentFileSelected = true;
        }
    }

    function previewFile(file) {
        while (previewContainer.firstChild) {
            previewContainer.removeChild(previewContainer.firstChild);
        }

        if (file.type.startsWith('image/')) {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function () {
                let img = document.createElement('img');
                img.src = reader.result;
                img.classList.add('preview');
                previewContainer.appendChild(img);

                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const maxWidth = 800;
                    const maxHeight = 600;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {
                        const resizedFile = new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(resizedFile);
                        fileElem.files = dataTransfer.files;

                        const resizedImg = document.createElement('img');
                        resizedImg.src = URL.createObjectURL(blob);
                        resizedImg.classList.add('preview');
                        previewContainer.innerHTML = '';
                        previewContainer.appendChild(resizedImg);

                        dropArea.style.display = 'none';
                        reselectBtn.style.display = 'block';
                    }, 'image/jpeg', 0.75);
                };
            };
        } else if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.controls = true;
            video.classList.add('preview');
            previewContainer.appendChild(video);

            dropArea.style.display = 'none';
            reselectBtn.style.display = 'block';
        }
    }

    function resetPreview() {
        while (previewContainer.firstChild) {
            previewContainer.removeChild(previewContainer.firstChild);
        }
        fileNameDisplay.innerText = '';
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!currentFileSelected) {
            fileElem.value = '';
            const defaultFilePath = 'public/img/dfo.jpg';
            e.target.appendChild(new FormData().append('img_path', defaultFilePath));
        }
    });
});
