document.addEventListener("DOMContentLoaded", function () {
    // ฟังก์ชันช่วยเหลือสำหรับการจัดการข้อมูลเริ่มต้น
    const prepareTableData = (preloadData, defaultData = []) => {
        return Array.isArray(preloadData) && preloadData.length > 0
            ? preloadData
            : defaultData;
    };

    // ฟังก์ชันตรวจสอบแถวว่างก่อนเพิ่ม
    const shouldAddNewRow = (table) => {
        const data = table.getData();
        return data.length === 0 || data[data.length - 1].detail.trim() !== "";
    };

    // ฟังก์ชันรีเซ็ตตาราง
    const resetTable = (tableId, confirmMessage) => {
        const table = window[tableId];
        if (table) {
            Swal.fire({
                title: "ยืนยันการล้างข้อมูล?",
                text: confirmMessage,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "ล้างข้อมูล",
                cancelButtonText: "ยกเลิก",
            }).then((result) => {
                if (result.isConfirmed) {
                    table.clearData();
                    Swal.fire("ล้างข้อมูลเรียบร้อย", "", "success");
                }
            });
        }
    };

    // ตารางที่ 1: ผลสัมฤทธิ์ของแผน
    if (document.querySelector("#results")) {
        const initialData = prepareTableData(window.preloadResults || [], []);

        function updateRowIndexes(table) {
            const rows = table.getRows();
            rows.forEach((row, i) => {
                row.update({ index: i + 1 });
            });
        }

        window.table = new Tabulator("#results", {
            layout: "fitColumns",
            height: "200px",
            data: initialData,
            reactiveData: true,
            movableRows: true,
            columns: [
                {
                    title: "ลำดับ",
                    width: 80,
                    hozAlign: "center",
                    headerSort: false,
                    formatter: (cell) => cell.getRow().getPosition() + 0,
                },
                {
                    title: "ตัววัดผลสัมฤทธิ์ของแผน",
                    field: "detail",
                    editor: "input",
                    hozAlign: "left",
                },
                {
                    title: "หน่วยวัด",
                    field: "unit",
                    editor: "input",
                    hozAlign: "center",
                    width: 150,
                },
            ],
            rowMoved: function () {
                updateRowIndexes(this);
            },
            dataLoaded: function () {
                updateRowIndexes(this);
            },
        });

        document
            .getElementById("reactivity-add")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                window.table
                    .addRow({
                        id: Date.now(),
                        detail: "",
                        unit: "",
                    })
                    .then(() => {
                        updateRowIndexes(window.table);
                    });
            });

        document
            .getElementById("reactivity-delete")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const lastRow = window.table.getRows().at(-1);
                if (lastRow) {
                    lastRow.delete();
                    updateRowIndexes(window.table);
                }
            });

        document
            .getElementById("submitFormBtn")
            ?.addEventListener("click", function () {
                window.table.getEditedCells().forEach((c) => c.cancelEdit());

                const result = window.table
                    .getData()
                    .map((r) => ({
                        detail: r.detail || "",
                        unit: r.unit || "",
                    }))
                    .filter(
                        (item) =>
                            item.detail.trim() !== "" || item.unit.trim() !== ""
                    );

                document.getElementById("resultsInput").value =
                    JSON.stringify(result);

                Swal.fire({
                    title: "ยืนยันการบันทึก?",
                    text: "ข้อมูลเดิมจะถูกแทนที่ด้วยข้อมูลใหม่",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "บันทึก",
                    cancelButtonText: "ยกเลิก",
                }).then((res) => {
                    if (res.isConfirmed) {
                        document.getElementById("resultForm").submit();
                    }
                });
            });

        updateRowIndexes(window.table);
    }

    // ตารางเป้าหมาย (Goals)
    if (document.querySelector("#goals")) {
        const initialData = prepareTableData(window.preloadGoals || [], []);

        function updateGoalIndexes(table) {
            table.getRows().forEach((row, i) => {
                row.update({ index: i + 1 });
            });
        }

        window.goalTable = new Tabulator("#goals", {
            layout: "fitColumns",
            height: "200px",
            data: initialData,
            reactiveData: true,
            movableRows: true,
            columns: [
                {
                    title: "ลำดับ",
                    field: "index",
                    width: 80,
                    hozAlign: "center",
                    headerSort: false,
                    formatter: (cell) => cell.getRow().getPosition() + 0,
                },
                {
                    title: "รายละเอียด",
                    field: "detail",
                    editor: "input",
                    hozAlign: "left",
                },
            ],
            rowMoved: function () {
                updateGoalIndexes(this);
            },
            dataLoaded: function () {
                updateGoalIndexes(this);
            },
        });

        document
            .getElementById("reactivity-add")
            ?.addEventListener("click", (e) => {
                e.preventDefault();

                const nextIndex = window.goalTable.getDataCount() + 1;

                window.goalTable
                    .addRow({ id: Date.now(), index: nextIndex, detail: "" })
                    .then(() => updateGoalIndexes(window.goalTable));
            });

        document
            .getElementById("reactivity-delete")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const lastRow = window.goalTable.getRows().at(-1);
                if (lastRow) {
                    lastRow.delete();
                    updateGoalIndexes(window.goalTable);
                }
            });

        // ✅ ปุ่มบันทึกทั้งหมด
        document
            .getElementById("submitFormBtn")
            ?.addEventListener("click", function () {
                // ยกเลิก edit
                [window.table1, window.table2, window.table3].forEach((t) => {
                    if (t)
                        t.getEditedCells().forEach((cell) => cell.cancelEdit());
                });

                // กรองข้อมูล
                const job =
                    window.table1
                        ?.getData()
                        .map((r) => r.detail || "")
                        .filter((d) => d.trim() !== "") || [];
                const depart =
                    window.table2
                        ?.getData()
                        .map((r) => r.detail || "")
                        .filter((d) => d.trim() !== "") || [];
                const people =
                    window.table3
                        ?.getData()
                        .map((r) => r.detail || "")
                        .filter((d) => d.trim() !== "") || [];

                document.getElementById("jobAdvantagesInput").value =
                    JSON.stringify(job);
                document.getElementById("departAdvantagesInput").value =
                    JSON.stringify(depart);
                document.getElementById("peopleAdvantagesInput").value =
                    JSON.stringify(people);

                Swal.fire({
                    title: "ยืนยันการบันทึก?",
                    text: "ข้อมูลเดิมจะถูกแทนที่ด้วยข้อมูลใหม่",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "บันทึก",
                    cancelButtonText: "ยกเลิก",
                }).then((res) => {
                    const goals = window.goalTable
                        .getData()
                        .filter((row) => row.detail?.trim() !== "");

                    console.log("💾 GOALS DATA:", goals);

                    document.getElementById("goalsInput").value =
                        JSON.stringify(goals);
                    if (res.isConfirmed) {
                        this.closest("form").submit();
                    }
                });
            });

        // ✅ ปุ่มรีเซ็ตตาราง goals
        document
            .getElementById("reset-goals")
            ?.addEventListener("click", () => {
                Swal.fire({
                    title: "ล้างข้อมูล?",
                    text: "คุณกำลังจะล้างข้อมูลเป้าหมายทั้งหมด",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ตกลง",
                    cancelButtonText: "ยกเลิก",
                }).then((res) => {
                    if (res.isConfirmed) {
                        window.goalTable.replaceData([]);
                    }
                });
            });

        // ✅ อัปเดตลำดับตอนโหลด
        updateGoalIndexes(window.goalTable);
    }
});
document
    .getElementById("submitGoalsBtn")
    ?.addEventListener("click", function () {
        // ยกเลิกการแก้ไขที่ยัง edit อยู่
        window.goalTable.getEditedCells().forEach((c) => c.cancelEdit());

        // กรองเฉพาะแถวที่มี detail
        const goals = window.goalTable
            .getData()
            .filter((row) => row.detail?.trim() !== "");

        // DEBUG
        console.log("📤 GOALS DATA SUBMIT:", goals);

        document.getElementById("goalsInput").value = JSON.stringify(goals);

        Swal.fire({
            title: "ยืนยันการบันทึก?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "บันทึก",
            cancelButtonText: "ยกเลิก",
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById("indicatorForm").submit();
            }
        });
    });
