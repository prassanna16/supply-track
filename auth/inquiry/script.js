function previewImage(event, input) {
  const preview = input.nextElementSibling;
  const file = event.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
  }
}

function deleteRow(btn) {
  const row = btn.closest('tr');
  row.remove();
}

function addProductRow() {
  const tbody = document.getElementById('productGroup');
  const newRow = tbody.rows[0].cloneNode(true);
  newRow.querySelector('.sno').textContent = tbody.rows.length + 1;
  newRow.querySelectorAll('input').forEach(input => input.value = '');
  newRow.querySelectorAll('img').forEach(img => img.style.display = 'none');
  newRow.querySelector('.supplier-error').textContent = '';
  tbody.appendChild(newRow);
}

function addSupplier(btn) {
  const group = btn.closest('td').querySelector('.supplier-group');
  const newInput = document.createElement('input');
  newInput.type = 'text';
  newInput.name = 'suppliers[0][]';
  newInput.placeholder = 'Supplier Name';
  newInput.onblur = function () { checkDuplicate(this); };
  group.appendChild(newInput);
  checkDuplicate(newInput);
}

function removeSupplier(btn) {
  const group = btn.closest('td').querySelector('.supplier-group');
  if (group.children.length > 1) {
    group.removeChild(group.lastChild);
  }
  const errorDiv = btn.closest('td').querySelector('.supplier-error');
  errorDiv.textContent = '';
}

function checkDuplicate(input) {
  const group = input.closest('.supplier-group');
  const inputs = group.querySelectorAll('input');
  const values = Array.from(inputs).map(i => i.value.trim().toLowerCase());
  const duplicates = values.filter((v, i, arr) => v && arr.indexOf(v) !== i);

  const errorDiv = input.closest('td').querySelector('.supplier-error');
  if (duplicates.length > 0) {
    errorDiv.textContent = `Duplicate supplier name: "${duplicates[0]}"`;
  } else {
    errorDiv.textContent = '';
  }
}