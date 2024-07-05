function triggerForm(formId)
{
    Swal.fire({
        title: "¿Estás segur@?",
        text: "No podrás revertir estos cambios",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, continuar",
        cancelButtonText: "Cancelar"
    }).then(function(result) {
        if(result.isConfirmed){
            document.getElementById(formId).submit();
        }

    });
}

$('[data-control="select2"]').select2();

$('input[data-flatpicker="true"]').flatpickr({
    enableTime: true,
    time_24hr: true,
    dateFormat: "d/m/Y H:i",
    locale: {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        },
        months: {
            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
            longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        }
    }
})