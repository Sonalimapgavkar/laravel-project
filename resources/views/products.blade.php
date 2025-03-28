<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card img {
            height: 150px;
            object-fit: cover;
        }
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Product Management</h1>

        <!-- Add/Edit Product Form -->
        <div class="card p-4 shadow-sm mb-4">
            <h3 id="formTitle">Add New Product</h3>
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId">
                <div class="mb-3">
                    <input type="text" id="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="mb-3">
                    <input type="number" id="price" class="form-control" placeholder="Price" required>
                </div>
                <div class="mb-3">
                    <input type="file" id="images" class="form-control" multiple>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="submitButton">Add Product</button>
                <button type="button" class="btn btn-secondary w-100 mt-2 d-none" id="cancelEdit" onclick="resetForm()">Cancel</button>
            </form>
        </div>

        <!-- Products List -->
        <h2 class="my-4">Products List</h2>
        <div id="products" class="row"></div>

        <!-- Cart Items Section -->
        <h2 class="my-4">Cart Items</h2>
        <div id="cart-items" class="row"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fetch all products
        function fetchProducts() {
            $.get('/api/products', function(data) {
                let html = '';
                data.forEach(product => {
                    html += `<div class="col-md-4">
                                <div class="card product-card shadow-sm mb-4">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="card-text">Rs.${product.price}</p>
                                        <div class="d-flex justify-content-center">
                                            ${product.images.map(image => `<img src="/storage/${image.image_path}" class="img-thumbnail mx-1">`).join('')}
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-success btn-sm" onclick="addToCart(${product.id})">Add to Cart</button>
                                            <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id}, '${product.name}', ${product.price})">Edit</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                });
                $('#products').html(html);
            });
        }

        // Fetch cart items
        function fetchCartItems() {
    $.get('/api/cart', function(data) {
        let html = '';

        data.forEach(item => {
            html += `<div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body text-center">
                                <h5 class="card-title">${item.product.name}</h5>
                                <p class="card-text">Rs.${item.product.price}</p>
                                <div class="d-flex justify-content-center">
                                    ${item.product.images.map(image => `<img src="/storage/${image.image_path}" class="img-thumbnail mx-1">`).join('')}
                                </div>
                            </div>
                        </div>
                    </div>`;
        });

        $('#cart-items').html(html);
    }).fail(function(xhr) {
        console.error(xhr.responseText); // Log error in console
        alert('Failed to load cart items.');
    });
}
//Add to card
        function addToCart(productId) {
    $.post('/api/cart', { user_id: 1, product_id: productId }, function(response) {
        alert('Product added to cart!');
        fetchCartItems();
    }).fail(function(xhr) {
        console.error(xhr.responseText); // Print error in console
        alert('Failed to add product to cart. Please check the console for details.');
    });
}

        // Handle Add/Edit Product
        $('#productForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData();
            let productId = $('#productId').val();

            formData.append('name', $('#name').val());
            formData.append('price', $('#price').val());
            
            if ($('#images')[0].files.length > 0) {
                $.each($('#images')[0].files, function(i, file) {
                    formData.append('images[]', file);
                });
            }

            if (productId) {
                $.ajax({
                    url: `/api/products/${productId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        fetchProducts();
                        alert('Product updated successfully');
                        resetForm();
                    }
                });
            } else {
                $.ajax({
                    url: '/api/products',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        fetchProducts();
                        alert('Product added successfully');
                        resetForm();
                    }
                });
            }
        });

        // Edit product (Prefill form)
        function editProduct(id, name, price) {
            $('#productId').val(id);
            $('#name').val(name);
            $('#price').val(price);
            $('#formTitle').text('Edit Product');
            $('#submitButton').text('Update Product').removeClass('btn-primary').addClass('btn-success');
            $('#cancelEdit').removeClass('d-none');
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        }

        // Reset form to Add Product mode
        function resetForm() {
            $('#productId').val('');
            $('#name').val('');
            $('#price').val('');
            $('#images').val('');
            $('#formTitle').text('Add New Product');
            $('#submitButton').text('Add Product').removeClass('btn-success').addClass('btn-primary');
            $('#cancelEdit').addClass('d-none');
        }

        // Delete product
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: `/api/products/${id}`,
                    type: 'DELETE',
                    success: function() {
                        fetchProducts();
                        alert('Product deleted successfully');
                    }
                });
            }
        }

        // Load products and cart on page load
        $(document).ready(function() {
            fetchProducts();
            fetchCartItems();
        });
    </script>
</body>
</html>
