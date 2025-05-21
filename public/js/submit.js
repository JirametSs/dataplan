let table = new Tabulator("#results", {
    height: "300px",
    layout: "fitColumns",
    columns: [
        { title: "รายละเอียดผลลัพธ์", field: "detail", editor: "input" },
        { title: "หน่วยนับ", field: "unit", editor: "input" },
    ],
    data: [],
});

document
    .getElementById("reactivity-add")
    .addEventListener("click", function () {
        table.addRow({});
    });

document
    .getElementById("reactivity-delete")
    .addEventListener("click", function () {
        let selected = table.getSelectedRows();
        selected.forEach((row) => row.delete());
    });

let goalTable = new Tabulator("#goal", {
    height: "300px",
    layout: "fitColumns",
    selectable: 1,
    columns: [
        { title: "เป้าหมายการดำเนินงาน", field: "detail", editor: "input" },
    ],
    data: [],
});

document.getElementById("reactivity-add").addEventListener("click", () => {
    goalTable.addRow({});
});

document.getElementById("reactivity-delete").addEventListener("click", () => {
    goalTable.getSelectedRows().forEach((row) => row.delete());
});

submitBtn.addEventListener("click", () => {
    indexTable.getData().then((data) => {
        document.getElementById("indicatorsInput").value = JSON.stringify(data);
        form.submit();
    });
});

let estimationTable = new Tabulator("#dataincome", {
    layout: "fitColumns",
    height: "auto",
    placeholder: "ยังไม่มีข้อมูลประมาณการ",
    columns: [
        { title: "รายละเอียด", field: "detail", editor: "input" },
        {
            title: "ปีงบประมาณ",
            field: "year",
            editor: "number",
            hozAlign: "center",
        },
    ],
    data: [],
});

document.getElementById("re-add").addEventListener("click", function () {
    estimationTable.addRow({});
});

document.getElementById("re-delete").addEventListener("click", function () {
    estimationTable.getRows().pop()?.delete();
});
