/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
window.onload = () => {
    //Recupération des communes
    alert("ok");
    let commune = document.querySelector("#fidele_commune");
    commune.addEventListener("change", function () {

        let form = this.closest("form");
        let data = this.name + "=" + this.value;
        console.log(data)
    });
},
        window.onload = () => {
    // On va chercher la div dans le HTML
    let calendarEl = document.getElementById('calendrier');

    // On instancie le calendrier
    let calendar = new FullCalendar.Calendar(calendarEl, {
        // On charge le composant "dayGrid"
        plugins: ['dayGrid'],
    });

    // On affiche le calendrier
    calendar.render();
}