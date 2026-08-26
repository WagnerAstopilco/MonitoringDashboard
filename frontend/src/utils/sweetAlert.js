import Swal from 'sweetalert2';

export const showSuccess = (message, title = 'Éxito') => {
    return Swal.fire({
        icon: 'success',
        title,
        text: message,
        confirmButtonText: 'Aceptar',
        customClass:{
            confirmButton:'btn btn-success'
        },
        buttonsStyling:false

    });
};

export const showError = (message, title = 'Error') => {
    return Swal.fire({
        icon: 'error',
        title,
        text: message,
        confirmButtonText: 'Aceptar',
        confirmButtonColor:'#d33',
    });
};

export const showWarning = (message, title = 'Advertencia') => {
    return Swal.fire({
        icon: 'warning',
        title,
        text: message,
        confirmButtonText: 'Aceptar',
        confirmButtonColor:'#f1c40f',
    });
};

export const confirmAction = async (
    message,
    title = '¿Estás seguro?'
) => {
    return await Swal.fire({
        icon: 'warning',
        title,
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        confirmButtonColor:'#008622',
        cancelButtonText: 'Cancelar',
        cancelButtonColor:'#d30000'
    });
};