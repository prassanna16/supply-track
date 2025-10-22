<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Input Sheet</title>
  <style>
    body {
      font-family: inherit;
      margin: 40px;
      background-color: #f9f9f9;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    th {
      background-color: #c8f3ed;
      text-align: left;
      padding: 12px;
      font-weight: bold;
    }
    td {
      padding: 10px;
      vertical-align: top;
    }
    input[type="text"],
    input[type="number"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #c8f3ed;
      transition: background-color 0.3s ease;
    }
    input:focus,
    select:focus {
      outline: none;
      border-color: #00bcd4;
    }
    input.filled,
    select.filled {
      background-color: #ffffff !important;
    }
    .image-preview {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-top: 5px;
    }
    .supplier-group input {
      margin-bottom: 5px;
      display: block;
      width: 100%;
      border-radius: 6px;
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
    .supplier-buttons button:hover {
      background-color: #0097a7;
    }
    button[type="submit"],
    button[type="button"] {
      padding: 10px 20px;
      border-radius: 6px;
      border: none;
      background-color: #00796b;
      color: white;
      font-weight: bold;
      cursor: pointer;
      margin-right: 10px;
    }
    button[type="submit"]:hover,
    button[type="button"]:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

<h2>Product Input Sheet</h2>
<form action="save_data.php" method="POST" enctype="multipart/form-data">
  <table>
    <thead>
      <tr>
        <th>S.No</th>
        <th>Buyer</th>
        <th>Image</th>
        <th>Style Model#</th>
        <th>Description</th>
        <th>Department</th>
        <th>Size Range</th>
        <th>Intake</th>
        <th>Season</th>
        <th>Fabric</th>
        <th>GSM</th>
        <th>Composition</th>
        <th>QTY</th>
        <th>Currency</th>
        <th>Target</th>
        <th>Suppliers</th>
      </tr>
    </thead>
    <tbody id="productGroup">
      <tr class="product-entry">
        <td><input type="number" name="sno[]" required></td>
        <td><input type="text" name="buyer[]" required></td>
        <td>
          <input type="file" name="image[]" accept="image/*" onchange="previewImage(event, this)">
          <img class="image-preview" src="#" alt="Preview" style="display:none;">
        </td>
        <td><input type="text" name="style[]"></td>
        <td><input type="text" name="description[]"></td>
        <td><input type="text" name="department[]"></td>
        <td><input type="text" name="size_range[]"></td>
        <td><input type="text" name="intake[]"></td>
        <td><input type="text" name="season[]"></td>
        <td><input type="text" name="fabric[]"></td>
        <td><input type="text" name="gsm[]"></td>
        <td><input type="text" name="composition[]"></td>
        <td><input type="number" name="qty[]" min="0" required></td>
        <td>
          <select name="currency[]" required>
            <option value="" disabled selected>Select Currency</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
            <option value="INR">INR</option>
            <option value="GBP">GBP</option>
            <option value="JPY">JPY</option>
            <option value="CNY">CNY</option>
          </select>
        </td>
        <td><input type="number" name="target[]" min="0.01" step="0.01" required></td>
        <td>
  <div class="supplier-group">
    <input type="text" name="suppliers[0][]" placeholder="Supplier Name">
  </div>
  <div class="supplier-buttons">
    <button type="button" onclick="addSupplier(this)">Add</button>
    <button type="button" onclick="removeSupplier(this)">Less</button>
  </div>
</td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top: 20px;">
    <button type="button" onclick="addProductRow()">➕ Add Row</button>
    <button type="submit" name="save">💾 Save All</button>
  </div>
</form>

<script>
function addProductRow() {
  const group = document.getElementById('productGroup');
  const entries = group.querySelectorAll('.product-entry');
  const lastEntry = entries[entries.length - 1];
  const clone = lastEntry.cloneNode(true);

  // Reset input values
  clone.querySelectorAll('input, select').forEach(el => {
    if (el.type !== 'file') el.value = '';
    if (el.tagName === 'SELECT') el.selectedIndex = 0;
    if (el.classList.contains('image-preview')) el.style.display = 'none';
  });

  // Update supplier name array index
  const supplierInputs = clone.querySelectorAll('.supplier-group input');
  supplierInputs.forEach(input => {
    input.name = `suppliers[${entries.length}][]`;
  });

  group.appendChild(clone);
}

function previewImage(event, input) {
  const preview = input.nextElementSibling;
  preview.src = URL.createObjectURL(event.target.files[0]);
  preview.style.display = 'block';
}

document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll("input[type='text'], input[type='number'], select");
  inputs.forEach(input => {
    input.addEventListener("input", () => {
      input.classList.toggle("filled", input.value.trim() !== "");
    });
  });
});
</script>

</body>
</html>