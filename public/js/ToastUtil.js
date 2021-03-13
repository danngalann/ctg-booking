function errorToast(text){
    Swal.fire({
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
    Swal.fire({
        icon: 'success',
        text,
        timer: 2000,
    })
}