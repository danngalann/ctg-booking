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