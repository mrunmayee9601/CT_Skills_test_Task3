<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Product Management Form</h1>

        <div class="card mb-4">
            <div class="card-header">Add New Product</div>
            <div class="card-body">
                <form id="product-form">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price per item</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Product List</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price per Item</th>
                            <th>Datetime Submitted</th>
                            <th>Total Value</th>
                            <th>Actions</th> 
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        </tbody>
                    <tfoot id="product-table-foot">
                        </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-product-form">
                    <input type="hidden" id="edit_product_id" name="id">
                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Quantity in stock</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price per item</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ===================================================================
        // Function to load products and display them in the table
        // ===================================================================
        function loadProducts() {
            $.ajax({
                url: '/products',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const tableBody = $('#product-table-body');
                    const tableFoot = $('#product-table-foot');
                    tableBody.empty();
                    tableFoot.empty();

                    let grandTotal = 0;

                    // Loop through each product and add a row to the table
                    data.forEach(function(product) {
                        let totalValue = product.quantity * product.price;
                        grandTotal += totalValue;
                        
                        let row = `<tr>
                            <td>${product.product_name}</td>
                            <td>${product.quantity}</td>
                            <td>$${parseFloat(product.price).toFixed(2)}</td>
                            <td>${product.datetime}</td>
                            <td>$${totalValue.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                        data-id="${product.id}"
                                        data-name="${product.product_name}"
                                        data-quantity="${product.quantity}"
                                        data-price="${product.price}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editProductModal">
                                    Edit
                                </button>
                            </td>
                        </tr>`;
                        tableBody.append(row); 
                    });
                    
                   
                    if (data.length > 0) {
                        let totalRow = `<tr>
                            <td colspan="5" class="text-end fw-bold">Sum Total:</td>
                            <td class="fw-bold">$${grandTotal.toFixed(2)}</td>
                        </tr>`;
                        tableFoot.append(totalRow);
                    }
                },
                error: function(err) {
                    console.error('Error loading products', err);
                }
            });
        }

        // ===================================================================
        // Handle the ADD form submission
        // ===================================================================
        $('#product-form').on('submit', function(event) {
            event.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: '/products',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#product-form')[0].reset();
                    loadProducts();
                },
                error: function(response) {
                    alert('Error saving product. Please check that all fields are filled correctly.');
                    console.error('Error submitting form', response);
                }
            });
        });

        // ===================================================================
        // NEW CODE: Handle the EDIT button click
        // ===================================================================
        $('#product-table-body').on('click', '.btn-edit', function() {
            
            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let quantity = $(this).data('quantity');
            let price = $(this).data('price');

            
            $('#edit_product_id').val(productId);
            $('#edit_product_name').val(productName);
            $('#edit_quantity').val(quantity);
            $('#edit_price').val(price);
        });

        // ===================================================================
        // Handle the EDIT form submission
        // ===================================================================

        $('#edit-product-form').on('submit', function(event) {
            event.preventDefault();

            let productId = $('#edit_product_id').val();
            let formData = $(this).serialize();

            $.ajax({
                url: '/products/' + productId, 
                type: 'PUT', 
                data: formData,
                success: function(response) { 
                    $('#editProductModal').modal('hide');
                    loadProducts();
                },
                error: function(response) {
                    alert('Error updating product.');
                    console.error('Error:', response);
                }
            });
        });
        loadProducts();

    });
</script>
    </body>
</html>