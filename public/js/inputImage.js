const img = document.querySelector('form .img-form');
const inputs = document.querySelectorAll('input[type="file"]');

if (img && inputs) {
    inputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.readAsDataURL(file);

            reader.onloadend = () => {
                img.src = reader.result;
            }
        });
    });
}