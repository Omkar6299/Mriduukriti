<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mriduukriti</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('frontend/assets/images/logo/mriduukriti logo_1.jpg') }}" type="image/x-icon">
    @include('frontend.layouts.css_link')
</head>

<body>
    @include('frontend.layouts.header')
    @yield('content')
    @include('frontend.layouts.footer')
    @include('frontend.layouts.script_link')
    @stack('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // add to cart script

        document.addEventListener("DOMContentLoaded", function() {
            const cartCountElement = document.getElementById("cart-count");

            // Setup CSRF token for Ajax
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                        "content")
                }
            });

            //    product add to card script
            $(".add-to-cart").on("click", function() {
                let productId = $(this).data("id");
                let productName = $(this).data("name");
                let productImage = $(this).data("image");

                $.ajax({
                    url: "/cart/add/" + productId,
                    type: "POST",
                    success: function(response) {
                        if (response.success) {
                            $("#cart-count").text(response.cart_count);

                            $("#cartToastName").text(productName);
                            $("#cartToastImage").attr("src", productImage);

                            let toastEl = document.getElementById("cartToast");
                            let toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        } else if (response.auth === false) {
                            // 🚀 Login Modal Open
                            let loginModal = new bootstrap.Modal(document.getElementById(
                                "loginModal"));
                            loginModal.show();
                        } else {
                            alert("Something went wrong!");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            //    product add to whistlist script
            $(".wishlist-btn").on("click", function() {
                let button = $(this);
                let productId = button.data("id");

                $.ajax({
                    url: "/whistlist/toggle/" + productId,
                    type: "POST",
                    success: function(response) {
                        if (response.success) {
                            // update count badge
                            $("#wishlist-count").text(response.Whistlist_count);
                            // toggle heart icon
                            let icon = button.find("i");
                            if (response.action === "added") {
                                icon.removeClass("bi-heart").addClass(
                                    "bi-heart-fill text-danger");
                                button.attr("title", "Remove from Wishlist");
                            } else if (response.action === "removed") {
                                icon.removeClass("bi-heart-fill text-danger").addClass(
                                    "bi-heart");
                                button.attr("title", "Add to Wishlist");
                            }
                            alert(response.message || "Something went wrong!");
                            $("#product-list").reload();
                            // $("#wishlist-items").reload();
                        } else if (response.auth === false) {
                            let loginModal = new bootstrap.Modal(document.getElementById(
                                "loginModal"));
                            loginModal.show();
                        } else {
                            alert(response.message || "Something went wrong!");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });









        });
    </script>
</body>

</html>
