import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-datatable="true"]').forEach((table) => {
        if (table.dataset.datatableInitialized === 'true') {
            return;
        }

        new DataTable(table, {
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            ordering: true,
            searching: true,
        });

        table.dataset.datatableInitialized = 'true';
    });
});