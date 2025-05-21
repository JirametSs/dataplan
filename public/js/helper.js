const i18n = {
    previousMonth: "ก่อนหน้า",
    nextMonth: "ถัดไป",
    months: [
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม",
    ],
    weekdays: [
        "อาทิตย์",
        "จันทร์",
        "อังคาร",
        "พุธ",
        "พฤหัสบดี",
        "ศุกร์",
        "เสาร์",
    ],
    weekdaysShort: ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."],
};

export const app_url = "http://localhost:8000";

export const me = async () => {
    const res = await fetch(`${app_url}/api/me`);
    const json = await res.json();

    return json.status && json.data.length > 0 ? json.data[0] : undefined;
};

/**url */
export const redirect = (dst = "") => {
    let path = dst == "" ? app_url : app_url + `/${dst}`;
    window.location.replace(path);
};

/**object */
export const createElement = (elName) => {
    return document.createElement(elName);
};

export const setAttribute = (el, attr = {}) => {
    for (const [key, value] of Object.entries(attr)) {
        el.setAttribute(key, value);
    }
};

/**Date time */
export const getI18N = () => {
    return i18n;
};
