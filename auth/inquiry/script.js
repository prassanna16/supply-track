function addProductRow() {
  const group = document.getElementById('productGroup');
  const entries = group.querySelectorAll('.product-entry');
  const lastEntry = entries[entries.length - 1];
  const clone = lastEntry.cloneNode(true);

  // Reset values
  clone.querySelectorAll('input, select').forEach(el => {
    if (el.type !== 'file') el.value = '';
    if (el.tagName === 'SELECT') el.selectedIndex = 0;
    if (el.classList.contains('image-preview')) el.style.display = 'none';
  });

  // Update supplier input names
  const entryIndex = entries.length;
  clone.querySelectorAll('.supplier-group input').forEach(input => {
    input.name = `suppliers[${entryIndex}][]`;
  });

  group.appendChild(clone);
  updateSno();
}

function deleteRow(button) {
  const row = button.closest('tr');
  const group = document.getElementById('productGroup');
  if (group.children.length > 1) {
    group.removeChild(row);
    updateSno();
  }
}

function updateSno() {
  document.querySelectorAll('.product-entry').forEach((row, index) => {
    row.querySelector('.sno').textContent = index + 1;
    row.querySelectorAll('.supplier-group input').forEach(input => {
      input.name = `suppliers[${index}][]`;
    });
  });
}

function toggleRow(button) {
  const collapsible = button.closest('.collapsible');
  collapsible.classList.toggle('active');
}

function previewImage(event, input) {
  const preview = input.nextElementSibling;
  preview.src = URL.createObjectURL(event.target.files[0]);
  preview.style.display = 'block';
}

function addSupplier(button) {
  const cell = button.closest('td');
  const group = cell.querySelector('.supplier-group');
  const row = button.closest('.product-entry');
  const index = [...document.querySelectorAll('.product-entry')].indexOf(row);
  const input = document.createElement('input');
  input.type = 'text';
  input.name = `suppliers[${index}][]`;
  input.placeholder = 'Supplier Name';
  input.onblur = function () {
    checkDuplicate(this);
  };
  group.appendChild(input);
}

function removeSupplier(button) {
  const cell = button.closest('td');
  const group = cell.querySelector('.supplier-group');
  if (group.children.length > 1) {
    group.removeChild(group.lastChild);
    cell.querySelector('.supplier-error').textContent = '';
  }
}

function checkDuplicate(input) {
  const value = input.value.trim().toLowerCase();
  const cell = input.closest('td');
  const errorBox = cell.querySelector('.supplier-error');
  errorBox.textContent = '';

  if (!value) return;

  const allInputs = cell.querySelectorAll('.supplier-group input');
  let count = 0;
  allInputs.forEach(i => {
    if (i.value.trim().toLowerCase() === value) count++;
  });

  if (count > 1) {
    errorBox.textContent = `❌ Supplier "${value}" already entered.`;
    input.style.borderColor = 'red';
  } else {
    input.style.borderColor = '';
  }
}