<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card img {
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Manage Cart</h1>

        <!-- Add to Cart Form -->
        <div class="card p-4 shadow-sm mb-4">
            <h3>Add Product to Cart</h3>
            <form id="addToCartForm">
                <div class="mb-3">
                    <select id="productSelect" class="form-control" required>
                        <option value="">Select a Product</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Add to Cart</button>
            </form>
        </div>

        <!-- Cart Items List -->
        <h2 class="my-4">Cart Items</h2>
        <div id="cart-items" class="row"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fetch products and populate dropdown
        function loadProducts() {
            $.get('/api/products', function(data) {
                let options = '<option value="">Select a Product</option>';
                data.forEach(product => {
                    options += `<option value="${product.id}">${product.name} - $${product.price}</option>`;
                });
                $('#productSelect').html(options);
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
                                        <p class="card-text">$${item.product.price}</p>
                                        <img src="/storage/${item.product.images[0]?.image_path}" class="img-thumbnail">
                                    </div>
                                </div>
                            </div>`;
                });
                $('#cart-items').html(html);
            });
        }

        // Handle form submission to add product to cart
        $('#addToCartForm').submit(function(e) {
            e.preventDefault();
            let productId = $('#productSelect').val();

            if (!productId) {
                alert('Please select a product.');
                return;
            }

            $.ajax({
                url: '/api/cart',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ product_id: productId }),
                success: function(response) {
                    alert(response.message);
                    fetchCartItems();
                },
                error: function(err) {
                    alert('Error adding product to cart.');
                }
            });
        });

        // Load products and cart items when page loads
        $(document).ready(function() {
            loadProducts();
            fetchCartItems();
        });
    </script>
</body>
</html>
