import { DataTable } from 'simple-datatables';
import 'simple-datatables/dist/style.css';

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('table.js-datatable').forEach((table) => {
    // Avoid double-init if the view is hot-reloaded
    if (table.dataset.datatableInitialized) return;
    table.dataset.datatableInitialized = '1';

    new DataTable(table, {
      searchable: true,
      sortable: true,
      perPage: 10,
      perPageSelect: [10, 25, 50, 100],
      labels: {
        placeholder: 'Search…',
        perPage: '{select} per page',
        noRows: 'No records found',
        info: 'Showing {start} to {end} of {rows} entries',
      },
    });
  });
});