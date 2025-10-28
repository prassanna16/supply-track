<?php
// supplier_price_modal.php
include '../../db.php';
?>
<div id="priceModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closePriceModal()">&times;</span>
    <h3>Enter Supplier Price</h3>

    <form id="supplierPriceForm">
      <div id="productDetails">
        <!-- Product details will be loaded here via AJAX -->
      </div>

      <label for="supplier_id">Supplier:</label>
      <select name="supplier_id" id="supplier_id" required>
        <?php
        $suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers");
        while ($s = mysqli_fetch_assoc($suppliers)) {
          echo "<option value='{$s['id']}'>{$s['name']}</option>";
        }
        ?>
      </select>

      <label for="price">Price:</label>
      <input type="number" step="0.01" name="price" id="price" required>

      <input type="hidden" name="product_id" id="product_id">
      <button type="submit">Save</button>
    </form>

    <div id="responseMessage"></div>
  </div>
</div>