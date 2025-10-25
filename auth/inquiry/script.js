function previewImage(event) {
  const preview = document.getElementById('preview');
  preview.src = URL.createObjectURL(event.target.files[0]);
  preview.style.display = 'block';
}

function checkDuplicate(input) {
  const value = input.value.trim().toLowerCase();
  const errorBox = document.getElementById('supplierError');
  errorBox.textContent = '';
  if (!value) return;

  const allInputs = document.querySelectorAll('#supplierGroup input');
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

function addSupplier() {
  const group = document.getElementById('supplierGroup');
  const input = document.createElement('input');
  input.type = 'text';
  input.name = 'suppliers[]';
  input.placeholder = 'Supplier Name';
  input.onblur = function () {
    checkDuplicate(this);
  };
  group.appendChild(input);
}

function removeSupplier() {
  const group = document.getElementById('supplierGroup');
  if (group.children.length > 1) {
    group.removeChild(group.lastChild);
    document.getElementById('supplierError').textContent = '';
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll(".product-form input[type='text'], .product-form input[type='number'], .product-form select");
  inputs.forEach(input => {
    input.addEventListener("input", () => {
      if (input.value.trim() !== "") {
        input.classList.add("filled");
      } else {
        input.classList.remove("filled");
      }
    });
  });
});