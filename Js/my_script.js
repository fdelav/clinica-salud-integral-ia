function getJsonData() {
    fetch('Json/doctores.json')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => createDoctorCards(data))
        .catch(error => console.error('Failed to fetch data:', error));
}

function createDoctorCards(data) {
    let temp = document.getElementById("doctor_card_template");
    if (!temp) {
        console.error("template not found");
        return;
    }

    data.doctores.forEach(doctor => {
        let clone = temp.content.cloneNode(true);

        // Nombre
        let card_name = clone.querySelector("#doctor_card_name");
        if (!card_name) { console.error("card name not found"); return; }
        card_name.textContent = doctor.nombre;

        // Especialidad (nuevo campo)
        let card_esp = clone.querySelector("#doctor_card_especialidad");
        if (card_esp && doctor.especialidad) {
            card_esp.textContent = doctor.especialidad;
        }

        // Imagen
        let card_img = clone.querySelector("#doctor_card_img");
        if (!card_img) { console.error("card image not found"); return; }
        card_img.src = `Json/doctores/${doctor.imagen}`;
        card_img.alt = doctor.nombre;
        card_img.onerror = function () {
            this.src = "Img/doctores/default.jpg";
        };

        document.getElementById("doctor_card_section").appendChild(clone);
    });
}

getJsonData();
