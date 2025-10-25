<script>
document.addEventListener("DOMContentLoaded", () => {
  // ✅ Image preview
  window.previewImage = function (event) {
    const preview = document.getElementById('preview');
    if (preview && event.target.files[0]) {
      preview.src = URL.createObjectURL(event.target.files[0]);
      preview.style.display = 'block';
    }
  };

  // ✅ Check for duplicate supplier names
  window.checkDuplicate = function (input) {
    const value = input.value.trim().toLowerCase();
    const errorBox = document.getElementById('supplierError');
    if (!value || !errorBox) return;

    errorBox.textContent = '';
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
  };

  // ✅ Add supplier input
  window.addSupplier = function () {
    const group = document.getElementById('supplierGroup');
    if (!group) return;

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'suppliers[]';
    input.placeholder = 'Supplier Name';
    input.onblur = function () {
      checkDuplicate(this);
    };
    group.appendChild(input);
  };

  // ✅ Remove supplier input
  window.removeSupplier = function () {
    const group = document.getElementById('supplierGroup');
    if (group && group.children.length > 1) {
      group.removeChild(group.lastChild);
      const errorBox = document.getElementById('supplierError');
      if (errorBox) errorBox.textContent = '';
    }
  };

  // ✅ Change background when filled
  const inputs = document.querySelectorAll("input[type='text'], input[type='number'], select");
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
</script>