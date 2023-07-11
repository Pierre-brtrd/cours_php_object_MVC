window.onload = () => {
    const switchs = document.querySelectorAll('.form-check-input.enabled');

    if (switchs) {
        switchs.forEach((input) => {
            input.addEventListener('change', (e) => {
                sendRequest(e.target);
            });
        });
    }
}

async function sendRequest(input) {
    const response = await fetch(`/admin/actifPoste/${input.dataset.id}`);

    if (response.status >= 200 && response.status <= 300) {
        const card = input.closest('.card');
        const text = card.querySelector('.text-actif-article');

        const data = (await response.json()).data;

        if (data.actif) {
            card.classList.remove('border-danger');
            card.classList.add('border-success');

            text.classList.remove('text-danger');
            text.classList.add('text-success');
            text.innerHTML = 'Actif';
        } else {
            card.classList.remove('border-success');
            card.classList.add('border-danger');

            text.classList.remove('text-success');
            text.classList.add('text-danger');
            text.innerHTML = 'Inactif';
        }
    } else {
        const section = document.querySelector('.container');

        const data = (await response.json()).data;

        const alert = document.createElement('div');
        alert.classList.add('alert');
        alert.classList.add('alert-danger');

        alert.innerHTML = data.message;

        if (section.querySelector('.alert')) {
            section.querySelector('.alert').remove();
        }

        section.prepend(alert);
    }
} 