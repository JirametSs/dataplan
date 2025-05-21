

    function confirmReset() {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการล้างข้อมูลในฟอร์มหรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ล้างข้อมูล!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("myForm").reset();
            Swal.fire(
                'ล้างข้อมูลแล้ว!',
                'ฟอร์มถูกล้างเรียบร้อย',
                'success'
            )
        }
    })
}

document.getElementById('submitFormBtn').addEventListener('click', function (e) {
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        text: "คุณต้องการบันทึกข้อมูลใช่ไหม?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'บันทึก',
        cancelButtonText: 'ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector('form').submit();
        }
    });
});
