document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filter-form");
    
    form.addEventListener("submit", function (e) {
        const startDate = document.getElementById("start_date").value;
        const endDate = document.getElementById("end_date").value;

        if (new Date(startDate) > new Date(endDate)) {
            alert("La fecha inicial no puede ser mayor que la fecha final.");
            e.preventDefault();
        }
    });
});