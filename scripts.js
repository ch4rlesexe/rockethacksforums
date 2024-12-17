function filterTable() {
    const input = document.getElementById('search-bar');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('applicationTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < td.length; j++) {
            if (td[j] && td[j].innerText.toLowerCase().indexOf(filter) > -1) {
                found = true;
            }
        }
        tr[i].style.display = found ? "" : "none";
    }
}

function exportToCSV() {
    let table = document.getElementById('applicationTable');
    let rows = table.querySelectorAll('tr');
    let csvContent = "data:text/csv;charset=utf-8,";

    rows.forEach((row) => {
        let cols = row.querySelectorAll('td, th');
        let rowData = Array.from(cols).map(col => '"' + col.innerText.replace(/"/g, '""') + '"').join(",");
        csvContent += rowData + "\r\n";
    });

    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "applications.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function sortTable(columnIndex) {
    let table = document.getElementById('applicationTable');
    let rows = Array.from(table.rows).slice(1);
    let isAscending = table.getAttribute("data-sort-order") === "asc";

    rows.sort((a, b) => {
        let cellA = a.cells[columnIndex].innerText.toLowerCase();
        let cellB = b.cells[columnIndex].innerText.toLowerCase();
        return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
    });

    rows.forEach(row => table.appendChild(row));
    table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");
}