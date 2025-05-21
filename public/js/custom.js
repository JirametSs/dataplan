document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.getElementById("sdate");
    const endInput = document.getElementById("edate");

    if (startInput && endInput) {
        startInput.addEventListener("change", calculateDuration);
        endInput.addEventListener("change", calculateDuration);
    }

    function calculateDuration() {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);

        const yearField = document.getElementById("year_long");
        const monthField = document.getElementById("month_long");
        const dayField = document.getElementById("day_long");

        if (!startInput.value || !endInput.value || endDate < startDate) {
            yearField.value = "";
            monthField.value = "";
            dayField.value = "";
            return;
        }

        let years = endDate.getFullYear() - startDate.getFullYear();
        let months = endDate.getMonth() - startDate.getMonth();
        let days = endDate.getDate() - startDate.getDate();

        if (days < 0) {
            months--;
            const prevMonth = new Date(
                endDate.getFullYear(),
                endDate.getMonth(),
                0
            ).getDate();
            days += prevMonth;
        }

        if (months < 0) {
            years--;
            months += 12;
        }

        yearField.value = years;
        monthField.value = months;
        dayField.value = days;
    }
});

const sidebar = document.getElementById("sidebar");
const btnClose = document.getElementById("btnSidebarClose");

// เปิดเมื่อ mouse อยู่ซ้ายสุด
// document.addEventListener('mousemove', function (e) {
//     if (e.clientX < 10) {
//         sidebar.style.transform = 'translateX(0)';
//     }
// });

btnClose?.addEventListener("click", () => {
    sidebar.style.transform = "translateX(-100%)";
});

// sidebar.addEventListener('mouseleave', function () {
//     if (window.innerWidth >= 768) {
//         sidebar.style.transform = 'translateX(-100%)';
//     }
// });

window.addEventListener("load", () => {
    if (window.innerWidth < 768) {
        sidebar.style.transform = "translateX(-100%)";
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("user-menu-button");
    const dropdown = document.getElementById("user-detail");

    btn.addEventListener("click", function (e) {
        dropdown.classList.toggle("d-none");
    });

    document.addEventListener("click", function (event) {
        if (!btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add("d-none");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const burger = document.getElementById("burgermenu");
    const sidebar = document.getElementById("sidebar");

    burger.addEventListener("click", function (e) {
        e.preventDefault(); // ป้องกันการ jump หน้า
        sidebar.classList.toggle("sidebar-hidden");
        sidebar.classList.toggle("sidebar-visible");
    });
});
// ====== SIDEBAR TOGGLE ======

const burger = document.getElementById("burgermenu");
const btnSidebarClose = document.getElementById("btnSidebarClose");

burger?.addEventListener("click", (e) => {
    e.preventDefault();
    sidebar.classList.toggle("sidebar-hidden");
    sidebar.classList.toggle("sidebar-visible");
});

btnSidebarClose?.addEventListener("click", (e) => {
    e.preventDefault();
    sidebar.classList.add("sidebar-hidden");
    sidebar.classList.remove("sidebar-visible");

    // ปิด sub-nav ทั้งหมด
    document.querySelectorAll("ul.sub-nav").forEach((el) => {
        el.classList.remove("sub-nav-show");
    });
});

// โหลดหน้า -> ปิด sidebar บนมือถือ
window.addEventListener("load", () => {
    if (window.innerWidth < 768) {
        sidebar.classList.add("sidebar-hidden");
        sidebar.classList.remove("sidebar-visible");
    }
});

// ====== SUBMENU TOGGLE ======
document.querySelectorAll("a.hasSubNav").forEach((el) => {
    el.addEventListener("click", (e) => {
        e.preventDefault();

        const next = el.nextElementSibling;
        if (next?.classList.contains("sub-nav")) {
            next.classList.toggle("sub-nav-show");
        }

        // เปิด sidebar ถ้ายังไม่เปิด
        sidebar.classList.add("sidebar-visible");
        sidebar.classList.remove("sidebar-hidden");
    });
});

// ====== PROFILE DROPDOWN TOGGLE ======
const profileBtn = document.getElementById("user-menu-button");
const profileMenu = document.getElementById("user-detail");
const profileClose = document.getElementById("icn-close-user-detail");

// เปิด dropdown
profileBtn?.addEventListener("click", function (e) {
    e.stopPropagation();
    profileMenu.classList.toggle("d-none");
});

// ปิด dropdown ด้วย X
profileClose?.addEventListener("click", function (e) {
    e.preventDefault();
    profileMenu.classList.add("d-none");
});

// คลิกข้างนอก -> ปิด dropdown
document.addEventListener("click", function (event) {
    if (
        !profileBtn.contains(event.target) &&
        !profileMenu.contains(event.target)
    ) {
        profileMenu.classList.add("d-none");
    }
});

burger?.addEventListener("click", function (e) {
    e.preventDefault();

    sidebar.classList.toggle("sidebar-visible");
    sidebar.classList.toggle("sidebar-hidden");
});

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

function btn_open() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("sidebar-hidden");
}

function calculateDuration() {
    const start = document.getElementById("start_date").value;
    const end = document.getElementById("end_date").value;

    if (start && end) {
        const startDate = new Date(start);
        const endDate = new Date(end);

        if (endDate < startDate) {
            alert("วันที่สิ้นสุดต้องไม่ก่อนวันที่เริ่ม");
            return;
        }

        let years = endDate.getFullYear() - startDate.getFullYear();
        let months = endDate.getMonth() - startDate.getMonth();
        let days = endDate.getDate() - startDate.getDate();

        if (days < 0) {
            months--;
            const previousMonth = new Date(
                endDate.getFullYear(),
                endDate.getMonth(),
                0
            );
            days += previousMonth.getDate();
        }

        if (months < 0) {
            years--;
            months += 12;
        }

        document.getElementById("duration_year").value = years;
        document.getElementById("duration_month").value = months;
        document.getElementById("duration_day").value = days;
    }
}
$(document).ready(function () {
    const $responsible = $("#responsible_person");
    const $phone = $("#phone");
    const $email = $("#email");

    function initResponsibleSelect() {
        if ($responsible.hasClass("select2-hidden-accessible")) {
            $responsible.select2("destroy");
        }

        $responsible.select2({
            placeholder: "-- เลือกผู้รับผิดชอบ --",
            allowClear: true,
            ajax: {
                url: "/form1/searchEmployee",
                data: function (params) {
                    return {
                        q: params.term || "",
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map((item) => ({
                            id: item.id,
                            text: `${item.text}`,
                            phone: item.phone,
                            email: item.email,
                        })),
                    };
                },
                delay: 300,
                cache: true,
            },
            templateResult: function (item) {
                if (!item.id) return item.text;
                return $(`
                    <div>
                        <strong>${item.text}</strong><br/>
                        <small>📞 ${item.phone ? item.phone : "-"} | ✉️ ${
                    item.email ? item.email : "-"
                }</small>
                    </div>
                `);
            },
            templateSelection: function (item) {
                return item.text || item.id;
            },
            minimumInputLength: 1,
            width: "100%",
        });
    }

    initResponsibleSelect();

    $responsible.on("select2:select", function () {
        const selected = $responsible.select2("data")[0];
        $phone.val(selected?.phone || "-");
        $email.val(selected?.email || "-");
    });
});

const dataFromTabulator = [
    { indicator: "จำนวนผู้เรียนในระบบ LE", unit: "คน" },
    { indicator: "Digital Transformation หน่วยงาน", unit: "%" },
    { indicator: "ร้อยละความพึงพอใจของผู้รับบริการ", unit: "%" },
];

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

const resultsTable = new Tabulator("#results", {
    height: "300px",
    layout: "fitColumns",
    reactiveData: true,
    data: [],
    columns: [
        { title: "ชื่อตัวชี้วัด", field: "detail", editor: "input" },
        { title: "หน่วย", field: "unit", editor: "input" },
    ],
});

document.getElementById("reactivity-add").addEventListener("click", () => {
    resultsTable.addRow({ detail: "", unit: "" });
});

document.getElementById("reactivity-delete").addEventListener("click", () => {
    const rows = resultsTable.getRows();
    if (rows.length > 0) {
        resultsTable.deleteRow(rows[rows.length - 1]);
    }
});
