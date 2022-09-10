
function closetoast() {
    $('#panel_tambah').removeClass('offcanvas-on');
    $('#overlay').removeClass('offcanvas-overlay');
    $('#formmodal').modal('hide');
}



function confirm_del(event) {
    console.log(event);

    Swal.fire({
        title: 'confirm',
        text: 'Data yang di pilih akan di hapus apakah anda yakin ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            del();
        }
    })
}



