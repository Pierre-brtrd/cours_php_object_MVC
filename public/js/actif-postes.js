// Script de switch visibilté en AJAX
window.onload = () => {
    let btns = document.querySelectorAll('.form-check-input.enabled');

    btns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            let xmlHttp = new XMLHttpRequest;
            let card = e.target.closest('.card');

            xmlHttp.open('GET', '/admin/actifPoste/' + e.target.dataset.id);

            xmlHttp.onload = () => {
                if (xmlHttp.readyState === xmlHttp.DONE) {
                    if (xmlHttp.status === 200) {
                        card.classList.remove((xmlHttp.response === 'border-success') ? 'border-danger' : 'border-success');
                        card.classList.add((xmlHttp.response === 'border-success') ? 'border-success' : 'border-danger');
                    } else {
                        console.errror('Error');
                    }
                }
            }

            xmlHttp.send();
        })
    })
}