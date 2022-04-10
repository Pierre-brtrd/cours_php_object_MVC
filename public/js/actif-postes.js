window.onload = () => {
    const btnEnabled = document.querySelectorAll(".form-check-input.enabled");

    for (let btn of btnEnabled) {
        btn.addEventListener("click", activer);
    }
}

function activer() {
    let xmlHttp = new XMLHttpRequest();

    xmlHttp.open("GET", '/admin/actifPoste/' + this.dataset.id);
    xmlHttp.send();
}