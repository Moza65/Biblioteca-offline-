function buscar() {
    const input = document.querySelector(".search-box input").value;

    if (input.trim() === "") {
        alert("Digite algo para buscar");
        return;
    }

    alert("Buscando por: " + input);
}