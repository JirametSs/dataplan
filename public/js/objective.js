document.addEventListener("DOMContentLoaded", function () {
    let objectiveCount = 1;
    let preloadData = [];

    if (
        Array.isArray(window.preloadObjectives) &&
        window.preloadObjectives.length > 0
    ) {
        preloadData = window.preloadObjectives.map((item, index) => ({
            id: item.id ?? index + 1,
            index: index + 1,
            name: item.detail || item.name || "",
        }));

        const maxId = Math.max(...preloadData.map((p) => p.id));
        objectiveCount = maxId + 1;
    }

    const table = new Tabulator("#objective", {
        height: "200px",
        layout: "fitColumns",
        reactiveData: true,
        movableRows: true,
        rowHandle: true,
        data: preloadData,
        columns: [
            {
                formatter: "handle",
                headerSort: false,
                width: 40,
                hozAlign: "center",
            },
            {
                title: "ลำดับ",
                field: "index",
                hozAlign: "center",
                width: 80,
                headerSort: false,
            },
            {
                title: "วัตถุประสงค์",
                field: "name",
                editor: "input",
                headerHozAlign: "center",
                hozAlign: "left",
            },
        ],
        rowMoved: function () {
            const currentData = table.getData();
            currentData.forEach((row, i) => (row.index = i + 1));
            table.replaceData(currentData);
        },
    });

    document
        .getElementById("reactivity-add")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            const currentData = table.getData();
            table.addRow({
                id: objectiveCount++,
                index: currentData.length + 1,
                name: "",
            });
        });

    document
        .getElementById("reactivity-delete")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            const currentData = table.getData();
            if (currentData.length > 0) {
                const lastRow = currentData[currentData.length - 1];
                table.deleteRow(lastRow.id).then(() => {
                    const updatedData = table.getData();
                    updatedData.forEach((row, i) => (row.index = i + 1));
                    table.replaceData(updatedData);
                });
            }
        });

    document
        .getElementById("submitFormBtn")
        ?.addEventListener("click", function () {
            // ยกเลิกการแก้ไขทั้งหมดก่อน
            table.getEditedCells().forEach((cell) => cell.cancelEdit());

            // เตรียมข้อมูลก่อน submit
            const data = table.getData().map((row) => ({
                id: row.id ?? null,
                detail: row.name || row.detail || "",
            }));

            const input = document.getElementById("objectiveInput");
            if (input) {
                input.value = JSON.stringify(data);
            }

            // แสดง SweetAlert ก่อนส่งฟอร์ม
            Swal.fire({
                title: "ยืนยันการบันทึก?",
                text: "คุณต้องการบันทึกข้อมูลใช่หรือไม่",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "บันทึก",
                cancelButtonText: "ยกเลิก",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest("form").submit();
                }
            });
        });
});

document.addEventListener("DOMContentLoaded", function () {
    const submitBtn = document.getElementById("submitFormBtn");
    if (!submitBtn) {
        console.error("❌ submitFormBtn not found");
        return;
    }

    const form = submitBtn.closest("form");
    if (!form) {
        console.error("❌ form not found");
        return;
    }

    submitBtn.addEventListener("click", function () {
        Swal.fire({
            title: "ยืนยันการบันทึก?",
            text: "คุณต้องการบันทึกข้อมูลใช่หรือไม่",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "บันทึก",
            cancelButtonText: "ยกเลิก",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
