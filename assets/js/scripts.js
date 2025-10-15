// Seleciona os elementos da página que vamos usar
const editPhotoButton = document.getElementById('edit-photo-btn');
const photoPopup = document.getElementById('popupEditarFoto');
const overlay = document.getElementById('overlay');

// --- Função para FECHAR o popup ---
function fecharPopups() {
    photoPopup.style.display = 'none';
    overlay.style.display = 'none';
}

// --- Lógica para ABRIR o popup ---
editPhotoButton.addEventListener('click', () => {
    // Quando clicado, mostra o popup e o overlay
    photoPopup.style.display = 'flex';
    overlay.style.display = 'block';
});

// Também fecha o popup se o usuário clicar no fundo escuro (overlay)
overlay.addEventListener('click', fecharPopups);

const inputFoto = document.getElementById('inputFotoPopup');
const previewFoto = document.getElementById('previewFotoPopup');
const fileNameDisplay = document.getElementById('fileNamePopup');
const originalSrc = previewFoto.src;
if (inputFoto) {
    inputFoto.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewFoto.src = e.target.result;
            }
            reader.readAsDataURL(file);
            fileNameDisplay.textContent = file.name;
        }
    });
}
