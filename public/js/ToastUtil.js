function errorToast(text){
    return Swal.fire({
        icon: 'error',
        title: 'Oops... Algo ha ido mal',
        text,
    })
}

function confirmationToast(title){
    return Swal.fire({
        title,
        showDenyButton: true,
        confirmButtonText: `Si`,
        denyButtonText: `No`,
    });
}

function successToast(text) {
    return Swal.fire({
        icon: 'success',
        showConfirmButton: false,
        text,
        timer: 1300,
    })
}