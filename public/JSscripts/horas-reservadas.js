document.addEventListener('DOMContentLoaded', main);

function main(){
    const mesaSelect = document.querySelector('#reserva_mesa');
    const fechaInput = document.querySelector('#reserva_fecha');
    const horaSelect = document.querySelector('#reserva_hora');

    function cargarHoras() {
        const mesaId = mesaSelect.value;
        const fecha = fechaInput.value;

        if (!mesaId || !fecha) return;

        fetch(`/horas-disponibles/${mesaId}/${fecha}`)
            .then(response => response.json())
            .then(data => {
                horaSelect.innerHTML = '<option value="">Selecciona una hora</option>';
                data.forEach(hora => {
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    horaSelect.appendChild(option);
                });
            });
    }

    mesaSelect.addEventListener('change', cargarHoras);
    fechaInput.addEventListener('change', cargarHoras);
}
