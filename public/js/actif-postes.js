// Script de switch visibilté en AJAX
window.onload = () => {
    let btns = document.querySelectorAll('.form-check-input.enabled');

    btns.forEach((btn) => {
        btn.addEventListener('click', () => {
            let xmlHttp = new XMLHttpRequest;
            let card = btn.closest('.card');
            
            xmlHttp.open('GET', '/admin/actifPoste/' + btn.dataset.id);

            xmlHttp.onload = () => {
                if(xmlHttp.readyState === xmlHttp.DONE) {
                    if(xmlHttp.status === 200) {
                        card.classList.remove((xmlHttp.response === 'border-success') ? 'border-danger' : 'border-success');
                        card.classList.add((xmlHttp.response === 'border-success') ? 'border-success' : 'border-danger');
                    }
                }
            }
            
            xmlHttp.send();
        })
    })
}