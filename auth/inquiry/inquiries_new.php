<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Entry Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #f1f8e9);
      padding: 30px;
    }
    h2 {
      text-align: center;
      color: #00796b;
      margin-bottom: 30px;
    }
    form {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      max-width: 1000px;
      margin: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th {
      background-color: #00bcd4;
      color: white;
      padding: 12px;
      text-align: left;
    }
    td {
      padding: 15px;
      vertical-align: top;
      background-color: #f9f9f9;
      border-bottom: 1px solid #ddd;
    }
    .sno {
      width: 50px;
      text-align: center;
      font-weight: bold;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
      color: #00796b;
    }
    input, select {
      width: 100%;
      padding: 8px;
      border-radius: 8px;
      border: 1px solid #b2dfdb;
      background-color: #e0f2f1;
      margin-top: 5px;
    }
    input:focus, select:focus {
      border-color: #00796b;
      outline: none;
    }
    .image-preview {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      display: none;
    }
    .supplier-group input {
      margin-bottom: 5px;
    }
    .supplier-buttons button {
      margin-right: 5px;
      padding: 6px 12px;
      border-radius: 6px;
      border: none;
      background-color: #00bcd4;
      color: white;
      cursor: pointer;
    }
    .supplier-error {
      color: red;
      font-size: 0.9em;
    }
    .actions {
      text-align: center;
      margin-top: 20px;
    }
    .actions button {
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      background-color: #00796b;
      color: white;
      font-weight: bold;
      cursor: pointer;
      margin: 0 10px;
    }
    @media (max-width: 768px) {
      td, th {
        display: block;
        width: 100%;
      }
      .actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

<h2>Product Entry Form</h2>
<form action="save_data.php" method="POST" enctype="multipart/form-data">
  <table>
    <thead>
      <tr>
        <th>S.No</th>
        <th>Details Row 1</th>
        <th>Details Row 2</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="productGroup">
      <tr class="product-entry">
        <td class="sno">1</td>
        <td>
          <label>Buyer</label><input type="text" name="buyer[]" required>
          <label>Image</label><input type="file" name="image[]" accept="image/*" onchange="previewImage(event, this)">
          <img class="image-preview" src="#" alt="Preview">
          <label>Style</label><input type="text" name="style[]">
          <label>Description</label><input type="text" name="description[]">
          <label>Department</label><input type="text" name="department[]">
        </td>
        <td>
          <label>Size Range</label><input type="text" name="size_range[]">
          <label>Intake</label><input type="text" name="intake[]">
          <label>Season</label><input type="text" name="season[]">
          <label>Fabric</label><input type="text" name="fabric[]">
          <label>GSM</label><input type="text" name="gsm[]">
          <label>Composition</label><input type="text" name="composition[]">
          <label>QTY</label><input type="number" name="qty[]" min="0">
          <label>Target</label><input type="number" name="target[]" step="0.01">
          <label>Currency</label>
          <select name="currency[]">
            <option value="" disabled selected>Select</option>
            <option value="USD">USD</option>
            <option value="INR">INR</option>
            <option value="EUR">EUR</option>
          </select>
          <label>Suppliers</label>
          <div class="supplier-group">
            <input type="text" name="suppliers[0][]" placeholder="Supplier Name">
          </div>
          <div class="supplier-buttons">
            <button type="button" onclick="addSupplier(this)">+</button>
            <button type="button" onclick="removeSupplier(this)">−</button>
          </div>
          <div class="supplier-error"></div>
        </td>
        <td><button type="button" onclick="deleteRow(this)">🗑️</button></td>
      </tr>
    </tbody>
  </table>

  <div class="actions">
    <button type="button" onclick="addProductRow()">➕ Add Row</button>
    <button type="submit">💾 Save All</button>
  </div>
</form>

<script>
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
    tbody.appendChild(newRow);
  }

  function addSupplier(btn) {
    const group = btn.closest('td').querySelector('.supplier-group');
    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.name = 'suppliers[0][]';
    newInput.placeholder = 'Supplier Name';
    group.appendChild(newInput);
  }

  function removeSupplier(btn) {
    const group = btn.closest('td').querySelector('.supplier-group');
    if (group.children.length > 1) {
      group.removeChild(group.lastChild);
    }
  }
</script>

</body>
</html>