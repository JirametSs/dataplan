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

// const sidebar = document.getElementById("sidebar");
// const btnClose = document.getElementById("btnSidebarClose");

// เปิดเมื่อ mouse อยู่ซ้ายสุด
// document.addEventListener('mousemove', function (e) {
//     if (e.clientX < 10) {
//         sidebar.style.transform = 'translateX(0)';
//     }
// });

// btnClose?.addEventListener("click", () => {
//     sidebar.style.transform = "translateX(-100%)";
// });

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
