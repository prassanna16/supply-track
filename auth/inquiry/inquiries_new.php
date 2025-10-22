<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Entry Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style_inquiry.css">
</head>
<body>

<h2>Product Entry Form</h2>
<form action="save_data.php" method="POST" enctype="multipart/form-data">
  <table>
    <thead>
      <tr>
        <th>S.No</th>
        <th>Details</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="productGroup">
      <tr class="product-entry">
        <td class="sno">1</td>
        <td>
          <div class="collapsible">
            <button type="button" class="toggle-btn" onclick="toggleRow(this)">▼</button>
            <div class="content">
              <label>Buyer</label><input type="text" name="buyer[]" required>
              <label>Image</label><input type="file" name="image[]" accept="image/*" onchange="previewImage(event, this)">
              <img class="image-preview" src="#" alt="Preview" style="display:none;">
              <label>Style</label><input type="text" name="style[]">
              <label>Description</label><input type="text" name="description[]">
              <label>Department</label><input type="text" name="department[]">
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
                <input type="text" name="suppliers[0][]" placeholder="Supplier Name" onblur="checkDuplicate(this)">
              </div>
              <div class="supplier-buttons">
                <button type="button" onclick="addSupplier(this)">+</button>
                <button type="button" onclick="removeSupplier(this)">−</button>
              </div>
              <div class="supplier-error"></div>
            </div>
          </div>
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

<script src="script.js"></script>
</body>
</html>